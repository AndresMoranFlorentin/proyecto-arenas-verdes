<?php

require_once './helpers/ToolsHelper.php';
require_once './servicios/ServicioReserva.php';
require_once './views/reservaView.php';
require_once './models/authModel.php';
require_once './helpers/sessionHelper.php';
require_once './models/ReservaModel.php';


class ReservaController
{   private static $disponibilidad=false;
    private $cel_washapp = "+54 9 2262 30-1388";
    private $view;
    private $helper;
    private $model;
    private $modelUser;
    private $toolsHelper;
    private $servicioR;
    function __construct()
    {
        $this->model = new ReservaModel();
        $this->modelUser = new authModel();
        $this->view = new ReservaView();
        $this->helper = new SessionHelper();
        $this->toolsHelper = new ToolsHelper();
        $this->servicioR = new ServicioReserva();
        $this->cel_washapp == "+54 9 2262 30-1388";
    }

    public function showHome()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->showHome($logueado, $rol,self::$disponibilidad);
    }

    public function renderPrecios()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->renderPrecios($logueado, $rol,self::$disponibilidad);
    }
    public function buscarParcelasDispo()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        if (
            !isset($_POST['inicio']) && !isset($_POST['fecha_fin'])
            && !isset($_POST['personas']) && !isset($_POST['tipo_de_vehiculo'])
            && !($this->servicioR->controlFechasInicioFin($_POST['inicio'], $_POST['fecha_fin']))
            && $_POST['personas'] <= 0
        ) {
            //vuelve a enviarte a la pagina de reservacion faltaria mejorar para que muestre un mensaje
            //de error por envio de datos incompletos 
            $this->view->reservacion(self::$disponibilidad);
        }
        $inicio = $_POST['inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $personas = $_POST['personas'];
        $tipo_de_vehiculo = $_POST['tipo_de_vehiculo'];
        //Las características de la parcela 
        $caracteristicas = $_POST['caracteristicas'];
        $con_fogon = in_array('fogon', $caracteristicas) ? 1 : 0;
        $con_electricidad = in_array('tomaElectrica', $caracteristicas) ? 1 : 0;
        $con_sombra = in_array('sombra', $caracteristicas) ? 1 : 0;
        $con_agua = in_array('agua', $caracteristicas) ? 1 : 0;
        $parcelas = $this->model->buscarParcelasDisponibles(
            $inicio,
            $fecha_fin,
            $personas,
            $tipo_de_vehiculo,
            $con_fogon,
            $con_electricidad,
            $con_sombra,
            $con_agua
        );
        //me trae las parcelas que se ajustan a las condiciones dadas por el usuario
        //hay 5 sectores en total: Carpa Familiar--Familiar--Joven--MotorHome
        $parcelas_por_sector=$this->servicioR->agruparPorSector($parcelas);        
        $this->view->mostrarParcelasDisponibles($parcelas,$parcelas_por_sector,$inicio,$fecha_fin,self::$disponibilidad);
    }
    //funcion encargada de recibir las condiciones de la reserva y simular los precios de una posible reserva
    public function simularPrecioReserva()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        if (
            !isset($_POST['edad_ninos4']) && !isset($_POST['edad_ninos12'])
            && !isset($_POST['edad_ninos20']) && (!isset($_POST['estancia']) && $_POST['estancia'] <= 0)
        ) {
            $this->view->renderPrecios($logueado, $rol,self::$disponibilidad);
        }
        $edadninos4 = $_POST['edad_ninos4']; //atributo de aquellas personas de hasta 4 años
        $edadninos12 = $_POST['edad_ninos12']; //atributo de aquellas personas entre 4 y 12 años
        $edadninos20 = $_POST['edad_ninos20']; //atributo de aquellas personas mayores de 12 años
        $tiempo_estancia = $_POST['estancia']; //el tiempo de estancia que calcula estar
        // Captura las características de la parcela 
        $caracteristicas = $_POST['caracteristicas'];
        $con_ducha = in_array('ducha', $caracteristicas) ? 1 : 0;
        $con_sanitario = in_array('sanitario', $caracteristicas) ? 1 : 0;

        $medio_transporte = $_POST['tipo_de_vehiculo'];
        $residente_loberia = $_POST['residentes'];
        $precio_final = $this->servicioR->calcularPrecio(
            $edadninos4,
            $edadninos12,
            $edadninos20,
            $tiempo_estancia,
            $con_ducha,
            $con_sanitario,
            $medio_transporte,
            $residente_loberia
        );

        $this->view->mostrarPrecioParcela($precio_final,self::$disponibilidad);
    }
    public function generarReservacion()
    {
        // Validar si el usuario está logueado y tiene un rol permitido
        // Verificar que llegaron todos los datos necesarios
        if (!$this->servicioR->validacionDatosReservacion($_POST)) {
            $mensaje = "Por favor, completa todos los campos obligatorios.";
            $tipo_mensaje = "error";
            $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);
            return;
        }

        // Recopilar y procesar datos
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $dni = $_POST['dni'];
        $fecha_inicio = $_POST['inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $menores = $_POST['menores'];
        $cuatroDoce = $_POST['cuatroDoce'];
        $doceMas = $_POST['doceMas'];
        $tipo_de_vehiculo = $_POST['tipo_de_vehiculo'];
        $caracteristicas = $_POST['caracteristicas'];

        // Convertir características a valores binarios
        $fogon = in_array('fogon', $caracteristicas) ? 1 : 0;
        $tomaElectrica = in_array('tomaElectrica', $caracteristicas) ? 1 : 0;
        $sombra = in_array('sombra', $caracteristicas) ? 1 : 0;
        $agua = in_array('agua', $caracteristicas) ? 1 : 0;
        $con_ducha = in_array('con_ducha', $caracteristicas) ? 1 : 0;
        $con_sanitario = in_array('con_sanitario', $caracteristicas) ? 1 : 0;

        $cantPersonas = $menores + $cuatroDoce + $doceMas;
        $dias_de_estancia = $this->servicioR->retornarDiasDeDiferencia($fecha_inicio, $fecha_fin);
        $precio_reserva = $this->servicioR->calcularPrecio($menores, $cuatroDoce, $doceMas, $dias_de_estancia, $con_ducha, $con_sanitario, $tipo_de_vehiculo, $_POST['personas']);

        // Validar si el usuario existe
        $id_user = $this->modelUser->findUserByDni($dni);
        $email_user=$id_user->email;
        if (empty($id_user)) {
            $mensaje = "Debe registrarse primero para poder hacer una reservación.";
            $tipo_mensaje = "error";
            $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);
            return;
        }

        // Buscar parcela disponible
        $id_parcela = $this->model->getParcelaDisponible($fecha_inicio, $fecha_fin, $cantPersonas, $tipo_de_vehiculo, $fogon, $tomaElectrica, $sombra, $agua);
        // busca si $id_servicio es encontrado
        $id_servicio = $this->getServicioAdicional($fogon, $tomaElectrica, $sombra, $agua);
        echo "<script>console.log('".addslashes("id servicio-> ".$id_servicio)."');</script>";
        if (!empty($id_parcela) && !empty($id_servicio)) {
            // Si llega aquí, $id_parcela es válido
            // Crear la reservación
            $identificador = $this->toolsHelper->generarIdentificador();
            $id_nueva_reserva = $this->model->nuevaReserva($id_user->id_usuario, $fecha_inicio, $fecha_fin, $tipo_de_vehiculo, $id_servicio, 'pendiente', $identificador);
            $this->model->crearRelacionParcela($id_nueva_reserva, $id_parcela);
           
            // Validar datos antes de generar el PDF
            if (empty($nombre) || empty($apellido) || empty($identificador) || empty($precio_reserva) || empty($this->cel_washapp)) {
                echo "Error: Datos incompletos para generar el PDF.";
            } else {
                try {
                    $archivoPdf= $this->toolsHelper->generarPDF($nombre, $apellido, $identificador, $precio_reserva, $this->cel_washapp);
                   
                    if ($archivoPdf) {
                        if ($this->toolsHelper->enviarCorreoConPDF($email_user, $nombre, $archivoPdf)) {
                            if(unlink($archivoPdf)){
                                //archivo eliminado exitosamente
                            }
                            else{
                                echo 'el archivo no se pudo eliminar';
                            }
                        } else {
                            echo "Error al enviar el correo.";
                        }
                    } else {
                        $mensaje = "Error al generar el comprobante de reserva.";
                        $tipo_mensaje = "error";
                        $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);
                    }
                    // Mostrar mensaje de éxito y redirigir
                    $mensaje = "Reservación creada exitosamente. El comprobante sera enviado a su correo electronico";
                    $tipo_mensaje="exito";
                    $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);

                } catch (Exception $e) {
                    echo "Error al generar el PDF: " . $e->getMessage();
                }
            }
        } 
        else{
            $mensaje = "No se ha encontrado una parcela con las características indicadas.";
            $tipo_mensaje = "error";
            $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);
     
        }
                   
    }
    /**
     * funcion encargada de buscar aquellos servicios que proveen ciertas parcelas
     * y en caso de que no exista---<< NO DEBERIA CREARLO >> deberian ya existir en la base de datos
     * precargado
     */
    private function getServicioAdicional($fogon, $tomaElectrica, $sombra, $agua)
    {
        $idServicio = $this->model->findServicio($fogon, $tomaElectrica, $sombra, $agua);
        //corregir, si no encuentra el servicio retornar null
        if (empty($idServicio)) { //no existe un servicio de esas caracteristicas
           return null;
        }
        return $idServicio;
    }
    /**
     * funcion encargada de marcar por parte del administrador como no disponible
     * aquellas parcelas que no pueden ser utilizadas
     */
    public function marcarNoDisponibleParcela($id_p){
        //como primer paso no se puede marcar como no disponible aquella
        //parcela o parcelas que ya esten reservadas, ya que generarian un
        //conflicto:
            //aun asi ahy dos soluciones para eso:
            //1->no dejar que se bloquee la parcela
            //2->buscar otra parcela que cumpla con los requisitos del usuario
            //si se encuentra otra parcela disponible entonces
            //modificar la reservacion del usuario para que apunte a la nueva
            //y entonces si marcar como No disponible la parcela original
            //entonces para marcar como no disponible una parcela debe pasar por varios controles
        
        if(!($this->model->estaReservada($id_p))){
            //quiere decir que no esta reservada por lo tanto
            //se la puede marcar como no disponible
            $this->model->marcarNoDisponibleParcela($id_p);
            //deberia actualizar la vista y mostrar ya sea un cartel u otra
            //cosa la parcela como ¡no! disponible
        }
        else{
            //en este caso al estar reservada se deberia
            //1)avisar que no se puede suspender la parcela
                    
        }

    }
    public function pedirReservacion()
    {
        $this->view->formSolicitarReservacion(null,self::$disponibilidad);
    }
    public function preguntasFrec()
    {
        $this->view->pregFrec(self::$disponibilidad);
    }
    public function reservacion()
    {
        $this->view->reservacion(self::$disponibilidad);
    }
    public static function setDisponibilidad($valor){
        self::$disponibilidad = $valor; // Acceso a variable estática
    }
        //Muestra los distintos sectores
        public function sectoresParcelas()
    {
        $this->view->parcelas();
    }
}
