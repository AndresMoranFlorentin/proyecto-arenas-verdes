<?php

require_once './helpers/ToolsHelper.php';
require_once './servicios/ServicioReserva.php';
require_once './views/reservaView.php';
require_once './models/authModel.php';
require_once './helpers/sessionHelper.php';
require_once './models/ReservaModel.php';
/**
 * Este archivo(Controlador) es el encargado de todas aquellas funciones 
 * que gestionan las reservas(crearlas, mostrarlas etc)
 */
class ReservaController
{   //variable booleana si la disponibilidad de reservas esta baja o no
    private static $disponibilidad=true;
    /** @var string numero de washapp*/
    private $cel_washapp = "+54 9 2262 30-1388";
    /** @var reservaView la vista de reservacion*/
    private $view;
    /** @var SessionHelper el ayudante del controller que contiene otras funciones*/
    private $helper;
    /** @var ToolsHelper el ayudante del controller que permite generar el pdf y enviarlo por email*/
    private $toolsHelper;
    /** @var ServicioReserva el ayudante del controller que contiene varias funciones importantes para este*/
    private $servicioR;
    /** @var ReservaModel el modelo de reservas */
    private $model;
    /** @var authModel  el modelo de usuarios*/
    private $modelUser;
    
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
    /**
     * Funcion encargada de devolver la vista del home(el inicio de la pagina)
     */
    public function showHome()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->showHome($logueado, $rol,self::$disponibilidad);
    }
    /**
     * Funcion encargada de mostrar la pagina de precios 
     * */
    public function renderPrecios()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->renderPrecios($logueado, $rol,self::$disponibilidad);
    }
    /**
     * Funcion encargada de buscar aquellas parcelas que se encuentren disponibles
     * entre una fecha dada y distintas caracteristicas que debe tener esta parcela
     */
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
        //captura de los datos enviados por el formulario:
        /** @var date fecha de inicio de cuando se encontraria disponible la parcela */    
        $inicio = $_POST['inicio'];
        /** @var date fecha de fin de cuando se encontraria disponible la parcela */    
        $fecha_fin = $_POST['fecha_fin'];
        //cantidad de personas que estarian dentro de la reservacion
        $personas = $_POST['personas'];
        $tipo_de_vehiculo = $_POST['tipo_de_vehiculo'];
        //Mas características de la parcela buscada
        $caracteristicas = $_POST['caracteristicas'];
        //fogon incluido o no
        $con_fogon = in_array('fogon', $caracteristicas) ? 1 : 0;
        //con electricidad o no
        $con_electricidad = in_array('tomaElectrica', $caracteristicas) ? 1 : 0;
        //con sombra o no
        $con_sombra = in_array('sombra', $caracteristicas) ? 1 : 0;
        //con agua o no
        $con_agua = in_array('agua', $caracteristicas) ? 1 : 0;
        //trae todas las parcelas que cumplan con esos requisitos
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
        //hay 5 sectores en total: Carpa Familiar--Familiar--Joven--MotorHome
        //la siguiente funcion hace un conteo de aquellas parcelas que se encontraron
        //pero agrupadas por los 5 sectores
        $parcelas_por_sector=$this->servicioR->agruparPorSector($parcelas); 
        //se muestra todas aquellas parcelas encontradas en detalle y por sector       
        $this->view->mostrarParcelasDisponibles($parcelas,$parcelas_por_sector,$inicio,$fecha_fin,self::$disponibilidad);
    }
    /**
     * Funcion encargada de calcular el precio de una reserva
     * dadas ciertas condiciones que llegan de un formulario
    */
     public function simularPrecioReserva()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        //control
        if (
            !isset($_POST['edad_ninos4']) && !isset($_POST['edad_ninos12'])
            && !isset($_POST['edad_ninos20']) && (!isset($_POST['estancia']) && $_POST['estancia'] <= 0)
        ) {
            $this->view->renderPrecios($logueado, $rol,self::$disponibilidad);
        }
        $edadninos4 = $_POST['edad_ninos4']; //cantidad de personas de hasta 4 años
        $edadninos12 = $_POST['edad_ninos12']; //cantidad de personas entre 4 y 12 años
        $edadninos20 = $_POST['edad_ninos20']; //cantidad de personas mayores de 12 años
        $tiempo_estancia = $_POST['estancia']; //el tiempo de estancia que calcula estar
        // Captura las características de la parcela 
        $caracteristicas = $_POST['caracteristicas'];

        $con_ducha = in_array('ducha', $caracteristicas) ? 1 : 0;
        $con_sanitario = in_array('sanitario', $caracteristicas) ? 1 : 0;

        $medio_transporte = $_POST['tipo_de_vehiculo'];
        $residente_loberia = $_POST['residentes'];
        //calcula el precio de la reservacion simulada
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
    /**
     * Funcion encargada de generar una reservacion en linea con
     * datos dados por el usuario tras pasar por varios controles
     */
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
        $nombre = $_POST['nombre'];//nombre de quien hace la reserva
        $apellido = $_POST['apellido'];//apellido de quien hace la reserva
        $dni = $_POST['dni'];//dni de quien hace la reserva
        $fecha_inicio = $_POST['inicio'];//fecha inicio de cuando inicia la reserva
        $fecha_fin = $_POST['fecha_fin'];//fecha fin de cuando termina la reserva
        $menores = $_POST['menores'];//numero de menores que iran
        $cuatroDoce = $_POST['cuatroDoce'];//numero de mayores a 4 hasta 12 años que iran
        $doceMas = $_POST['doceMas'];//numero de mayores a 12 años que iran
        $tipo_de_vehiculo = $_POST['tipo_de_vehiculo'];//el tipo de vehiculo que usara(aunque esta la opcion de no elegir ninguno)
       
        $caracteristicas = $_POST['caracteristicas'];

        // Convertir características a valores binarios
        $fogon = in_array('fogon', $caracteristicas) ? 1 : 0;
        $tomaElectrica = in_array('tomaElectrica', $caracteristicas) ? 1 : 0;
        $sombra = in_array('sombra', $caracteristicas) ? 1 : 0;
        $agua = in_array('agua', $caracteristicas) ? 1 : 0;
        $con_ducha = in_array('con_ducha', $caracteristicas) ? 1 : 0;
        $con_sanitario = in_array('con_sanitario', $caracteristicas) ? 1 : 0;
        //suma de la cantidad de personas en total
        $cantPersonas = $menores + $cuatroDoce + $doceMas;
        //funcion que calcula los dias de estancia de la reservacion
        $dias_de_estancia = $this->servicioR->retornarDiasDeDiferencia($fecha_inicio, $fecha_fin);
        //de nuevo se calcula el precio del costo de la reservacion
        $precio_reserva = $this->servicioR->calcularPrecio($menores, $cuatroDoce, $doceMas, $dias_de_estancia, $con_ducha, $con_sanitario, $tipo_de_vehiculo, $_POST['personas']);

        // Validar si el usuario existe mediante su dni
        $id_user = $this->modelUser->findUserByDni($dni);

        if (empty($id_user)) {
            $mensaje = "Debe registrarse primero para poder hacer una reservación.";
            $tipo_mensaje = "error";
            $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);
            return;
        }
        //se obtiene el email del usuario
        $email_user=$id_user->email;
        // Buscar la parcela disponible
        $id_parcela = $this->model->getParcelaDisponible($fecha_inicio, $fecha_fin, $cantPersonas, $tipo_de_vehiculo, $fogon, $tomaElectrica, $sombra, $agua);
        // busca si el servicio es encontrado en la bbdd
        $id_servicio = $this->getServicioAdicional($fogon, $tomaElectrica, $sombra, $agua);
        //echo "<script>console.log('".addslashes("id parcela-> ".$id_parcela)."');</script>";
        //echo "<script>console.log('".addslashes("id servicio-> ".$id_servicio)."');</script>";
        //controla si se encontro una parcela indicada
        if (!empty($id_parcela) && !empty($id_servicio)) {
            // Si llega aquí, se encontro una parcela al menos que coincide con lo que buscaba el usuario
            // Crea la reservación con los datos recolectados:
            // se genera el identificador de la reservacion
            $identificador = $this->toolsHelper->generarIdentificador();
            // en esta funcion se genera la reserva y a su vez se devuelve el id de la reserva que se genero
            $id_nueva_reserva = $this->model->nuevaReserva($id_user->id_usuario, $fecha_inicio, $fecha_fin, $tipo_de_vehiculo, $id_servicio, 'pendiente', $identificador);
            // se realiza la conexion entre la nueva reserva y la parcela que sera ocupada
            $this->model->crearRelacionParcela($id_nueva_reserva, $id_parcela);
            // ya la reservacion fue creada, en el siguiente paso se genera un comprobante pdf
            // Validacion de los datos antes de generar el PDF
            if (empty($nombre) || empty($apellido) || empty($identificador) || empty($precio_reserva) || empty($this->cel_washapp)) {
                $mensaje = "Reservación creada exitosamente. Datos incompletos para generar el comprobante y enviarlo a su email";
                $tipo_mensaje="cuidado";
                $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);
            } else {
                try {
                    //se genera el archivo pdf con los datos pasados
                    $archivoPdf= $this->toolsHelper->generarPDF($nombre, $apellido, $identificador, $precio_reserva, $this->cel_washapp);
                   //si pudo crearlo..
                    if ($archivoPdf) {
                        //envia a el correo el comprobante
                        if ($this->toolsHelper->enviarCorreoConPDF($email_user, $nombre, $archivoPdf)) {
                            //si lo envio entonces procede a eliminar el archivo
                            if(unlink($archivoPdf)){
                            }
                            else{
                                //en el futuro se deberia buscar volver a eliminar el archivo
                                unlink($archivoPdf);
                                //echo 'el archivo no se pudo eliminar';
                            }
                        } else {
                            $mensaje = "Reservacion Exitosa. Error al enviar el correo.";
                            $tipo_mensaje = "cuidado";
                            $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);
                        }
                    } else {
                        $mensaje = "Reservacion Exitosa. Error al generar el comprobante de reserva.";
                        $tipo_mensaje = "cuidado";
                        $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);
                    }
                    // Mostrar mensaje de éxito y redirigir
                    $mensaje = "Reservación creada exitosamente. El comprobante fue enviado a su correo electronico";
                    $tipo_mensaje="exito";
                    $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);

                } catch (Exception $e) {
                    //echo "Error al generar el PDF: " . $e->getMessage();
                    $mensaje = "Reservación creada exitosamente. Error al generar el PDF";
                    $tipo_mensaje="cuidado";
                    $this->view->mostrarFormularioReservacion($mensaje, $tipo_mensaje,self::$disponibilidad);
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
     * Funcion que permite al usuario cancelar aquella reservacion que le 
     * interese
     * ---notar que falta agregarle el logueo para que solo el usuario registrado
     * ---o el superadmin pueda acceder a el, y al usuario solo se le permitira 
     * ---cancelar sus reservas dentro de un plazo de tiempo
     */
    public function cancelarReserva(){
        //falta comprobar que estes logueado
        $id_reserva=$_POST['id'];
        //primero se debe eliminar la relacion reserva parcela
        $elimino_relacion=$this->model->eliminarRelacionParcelaReserva($id_reserva);
        //luego eliminar la reservacion
        if($elimino_relacion){
            //se procede a eliminar la reserva
            $elimino_reserva=$this->model->eliminarReserva($id_reserva);
            if($elimino_reserva){
                echo "mostrar en la vista que se elimino la reservacion con exito";
            }
        }
        else{
            echo 'mostrar que debido a un error de conexion no se pudo eliminar la reservacion';
        }
    }
    /**
     * Funcion encargada de buscar aquellos servicios que proveen ciertas parcelas
     * @param int $fogon (0 o 1 false o true) 
     * @param int $tomaElectrica (0 o 1 false o true)
     * @param int $sombra (0 o 1 false o true)
     * @param int $agua (0 o 1 false o true)
     * 
     * @return string $idServicio el id del servicio encontrado
     */
    private function getServicioAdicional($fogon, $tomaElectrica, $sombra, $agua)
    {
        $idServicio = $this->model->findServicio($fogon, $tomaElectrica, $sombra, $agua);
        //si no encuentra el servicio retornar null
        if (empty($idServicio)) { //no existe un servicio de esas caracteristicas
           return null;
        }
        return $idServicio;
    }
   /**
    * Funcion que lleva a la pagina que muestra el formulario de la reservacion
    */
    public function pedirReservacion()
    {
        $this->view->formSolicitarReservacion(null,self::$disponibilidad);
    }
    /**
     * Funcion que lleva a la seccion de la pagina que muestra las preguntas mas frecuentes
     */
    public function preguntasFrec()
    {
        $this->view->pregFrec(self::$disponibilidad);
    }
      /**
     * Funcion que lleva a la seccion de la pagina que muestra las preguntas mas frecuentes
     */
    public function reservacion()
    {
        $this->view->reservacion(self::$disponibilidad);
    }
    /**
     * Funcion para modificar la disponibilidad de reservas desde otro sitio
     */
    public static function setDisponibilidad($valor){
        self::$disponibilidad = $valor; // Acceso a variable estática
    }
    /**
     * Funcion que devuelve el valor de la disponibilidad
     * @return boolean devuelve el valor de disponibilidad
     */
    public static function obtenerDisponibilidad()
    {
        return self::$disponibilidad;
    }
}
