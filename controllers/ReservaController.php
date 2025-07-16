<?php
require_once 'BaseController.php'; // Incluir la clase abstracta
require_once './models/ReservaModel.php';
require_once './models/authModel.php';
require_once './views/reservaView.php';
require_once './helpers/sessionHelper.php';
require_once './helpers/ToolsHelper.php';
require_once './servicios/ServicioReserva.php';
require_once './views/authView.php';
/**
 * Este archivo(Controlador) es el encargado de todas aquellas funciones 
 * que gestionan las reservas(crearlas, mostrarlas etc)
 */
class ReservaController extends BaseController
{
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
    /** @var authView el modelo de la vista de autenticacion*/
    private $authView;

    function __construct()
    {
        $this->model = new ReservaModel();
        $this->modelUser = new authModel();
        $this->view = new reservaView();
        $this->helper = new SessionHelper();
        $this->toolsHelper = new ToolsHelper();
        $this->servicioR = new ServicioReserva();
        $this->cel_washapp == "+54 9 2262 30-1388";
        $this->authView = new authView();
    }
    /**
     * Funcion encargada de devolver la vista del home(el inicio de la pagina)
     */
    public function showHome()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->showHome($logueado, $rol, BaseController::getDisponibilidad());
    }
    /**
     * Funcion encargada de mostrar la pagina de precios 
     * */
    public function renderPrecios()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $precios = $this->model->getPreciosLista();
        $this->view->renderPrecios($logueado, $rol, $precios, BaseController::getDisponibilidad());
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
            !isset($_POST['inicio']) || !isset($_POST['fecha_fin'])
            || !isset($_POST['personas']) && !isset($_POST['tipo_de_vehiculo'])
            || !($this->servicioR->controlFechasInicioFin($_POST['inicio'], $_POST['fecha_fin']))
            || ($_POST['personas'] <= 0))
       {
            //vuelve a enviarte a la pagina de reservacion faltaria mejorar para que muestre un mensaje
            //de error por envio de datos incompletos 
            $mostrar=true;
            $mensaje="Uno o varios de los parametros pedidos fueron erroneamente ingresados";
            $this->view->mostrarParcela($rol, $logueado, BaseController::getDisponibilidad(),$mostrar,$mensaje);
            return;
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
        $parcelas_por_sector = $this->servicioR->agruparPorSector($parcelas);
        //se muestra todas aquellas parcelas encontradas en detalle y por sector 
        $this->view->mostrarParcelasDisponibles($rol, $logueado, $parcelas, $parcelas_por_sector, $inicio, $fecha_fin, BaseController::getDisponibilidad());
    }
    /**
     * Funcion encargada de calcular el precio de una reserva
     * dadas ciertas condiciones que llegan de un formulario
     */
    public function simularPrecioReserva()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $precios = $this->model->getPreciosLista();

        //control
        if (
            !isset($_POST['edad_ninos4']) && !isset($_POST['edad_ninos12'])
            && !isset($_POST['edad_ninos20']) && (!isset($_POST['estancia']) && $_POST['estancia'] <= 0)
        ) {
            $precios = $this->model->getPreciosLista();
            $this->view->renderPrecios($logueado, $rol, $precios, BaseController::getDisponibilidad());
        }
        $edadninos4 = $_POST['edad_ninos4']; //cantidad de personas de hasta 4 años
        $edadninos12 = $_POST['edad_ninos12']; //cantidad de personas entre 4 y 12 años
        $edadninos20 = $_POST['edad_ninos20']; //cantidad de personas mayores de 12 años
        
        $fecha_inicio = $_POST['estancia_ingreso']; //fecha inicio de cuando inicia la reserva
        $fecha_fin = $_POST['estancia_salida']; //fecha fin de cuando termina la reserva
        $tiempo_estancia = $this->servicioR->retornarDiasDeDiferencia($fecha_inicio, $fecha_fin);           

        // Captura las características de la parcela 
        $caracteristicas = $_POST['caracteristicas'];

        $con_ducha = in_array('ducha', $caracteristicas) ? 1 : 0;
        $con_sanitario = in_array('sanitario', $caracteristicas) ? 1 : 0;

        $medio_transporte = $_POST['tipo_de_vehiculo'];
        $residente_loberia = $_POST['localidad'];
        
        if ($residente_loberia == 'loberia') {
            $residente_loberia = 1;
        } else {
            $residente_loberia = 0;
        }
        $tipo_de_estadia=$_POST['tipo_estadia'];
       
        //calcula el precio de la reservacion simulada
        $precio_final = $this->servicioR->calcularPrecio(
            $edadninos4,
            $edadninos12,
            $edadninos20,
            $tiempo_estancia,
            $con_ducha,
            $tipo_de_estadia,
            $medio_transporte,
            $residente_loberia
        );

        $this->view->mostrarPrecioParcela($rol, $logueado, $precio_final, $precios, BaseController::getDisponibilidad());
    }
    /**
     * Funcion encargada de generar una reservacion en linea con
     * datos dados por el usuario tras pasar por varios controles
     */
    public function generarReservacion()
    {
        // Validar si el usuario está logueado y tiene un rol permitido
        $logueado = $this->helper->checkUser();
        //se verifica que el usuario esta al menos logueado
        if ($logueado) {
            // Recopilar y procesar datos
            $nombre = $_POST['nombre']; //nombre de quien hace la reserva
            $apellido = $_POST['apellido']; //apellido de quien hace la reserva
            $dni = $_POST['dni']; //dni de quien hace la reserva
            $fecha_inicio = $_POST['inicio']; //fecha inicio de cuando inicia la reserva
            $fecha_fin = $_POST['fecha_fin']; //fecha fin de cuando termina la reserva

            $tipo_de_estadia=$_POST['tipo_estadia'];//tipo de estadia si es acampar o pasar el dia, muy importante a la hora de generar el precio

            $menores = $_POST['menores']; //numero de menores que iran
            $cuatroDoce = $_POST['cuatroDoce']; //numero de mayores a 4 hasta 12 años que iran
            $doceMas = $_POST['doceMas']; //numero de mayores a 12 años que iran
            $tipo_de_vehiculo = $_POST['tipo_de_vehiculo']; //el tipo de vehiculo que usara(aunque esta la opcion de no elegir ninguno)
           
            //en caso de que no haya seleccionado ninguna caracteristica
            //queda el array de caracteristicas vacio, pero no null
            $caracteristicas = isset($_POST['caracteristicas']) ? $_POST['caracteristicas'] : [];

            // Convertir características a valores binarios
            $fogon = in_array('fogon', $caracteristicas) ? 1 : 0;
            $tomaElectrica = in_array('tomaElectrica', $caracteristicas) ? 1 : 0;
            $sombra = in_array('sombra', $caracteristicas) ? 1 : 0;
            $agua = in_array('agua', $caracteristicas) ? 1 : 0;
            $con_ducha = in_array('con_ducha', $caracteristicas) ? 1 : 0;
            //suma de la cantidad de personas en total
            $cantPersonas = $menores + $cuatroDoce + $doceMas;
            //informacion de si paga en efectivo o por transferencia
            $tipo_pago=$_POST['tipo_pago'];
            $mostrar_qr=false;
            if($tipo_pago=="transferencia"){
                $mostrar_qr=true;
            }
            else{
                $mostrar_qr=false;
            }
            //funcion que calcula los dias de estancia de la reservacion
            $dias_de_estancia = $this->servicioR->retornarDiasDeDiferencia($fecha_inicio, $fecha_fin);           
           
            // Buscar la parcela disponible         
            // Verificar que llegaron todos los datos necesarios
            if (!$this->servicioR->validacionDatosReservacion($_POST)) {
                $mensaje = "Debera asegurarse de que no introdujo datos erroneos y que \n completo todos los campos obligatorios del formulario.";
                $tipo_mensaje = "error";
                $this->irAReservacion($mensaje,$tipo_mensaje,$mostrar_qr);
                return;
            }
            $datos_user = $this->modelUser->findUserByDni($dni);  
             //se obtiene el email del usuario
            $email_user = $datos_user->email;
            //se obtiene el id del usuario  
            $id_user = $datos_user->id;
            //se carga la variable para saber si es residente o no de loberia
            $residente = $this->modelUser->userIsResident($id_user) ? 1 : 0;

            $id_parcela = $this->model->getParcelaDisponible($fecha_inicio, $fecha_fin, $cantPersonas, $tipo_de_vehiculo, $fogon, $tomaElectrica, $sombra, $agua);
            //una vez asegurado que existe el usuario se procede a generar el precio de la reservacion
            $precio_reserva = $this->servicioR->calcularPrecio($menores, $cuatroDoce, $doceMas, $dias_de_estancia, $con_ducha,$tipo_de_estadia,$tipo_de_vehiculo, $residente);

            // busca si el servicio es encontrado en la bbdd
            $id_servicio = $this->getServicioAdicional($fogon, $tomaElectrica, $sombra,$con_ducha, $agua);
            //controla si se encontro una parcela indicada
            
            if (!empty($id_parcela) && !empty($id_servicio)) {
                // Si llega aquí, se encontro una parcela al menos que coincide con lo que buscaba el usuario
                // Crea la reservación con los datos recolectados:
                // se genera el identificador de la reservacion
                $identificador = $this->toolsHelper->generarIdentificador();
                // en esta funcion se genera la reserva y a su vez se devuelve el id de la reserva que se genero
                $id_nueva_reserva = $this->model->nuevaReserva($id_user, $menores, $cuatroDoce, $doceMas, $fecha_inicio, $fecha_fin, $tipo_de_vehiculo, $id_servicio, 'pendiente', $identificador,$precio_reserva);
                // se realiza la conexion entre la nueva reserva y la parcela que sera ocupada
                $this->model->crearRelacionParcela($id_nueva_reserva, $id_parcela);

                // ya la reservacion fue creada, en el siguiente paso se genera un comprobante pdf
                // Validacion de los datos antes de generar el PDF
                if (empty($nombre) || empty($apellido) || empty($identificador) || empty($precio_reserva) || empty($this->cel_washapp)) {
                    $mensaje = "Reservación creada exitosamente. Datos incompletos para generar el comprobante y enviarlo a su email";
                    $tipo_mensaje = "cuidado";
                    $this->irAReservacion($mensaje,$tipo_mensaje,$mostrar_qr);
                    return;
                } else {
                    try {
                        //se genera el archivo pdf con los datos pasados
                        $archivoPdf = $this->toolsHelper->generarPDF($nombre, $apellido, $identificador, $precio_reserva, $this->cel_washapp);
                        //si pudo crearlo..
                        if ($archivoPdf) {
                            //envia a el correo el comprobante
                            if ($this->toolsHelper->enviarCorreoConPDF($email_user, $nombre, $archivoPdf)) {
                                //si lo envio entonces procede a eliminar el archivo
                                if (unlink($archivoPdf)) {
                                } else {
                                    //en el futuro se deberia buscar volver a eliminar el archivo
                                    unlink($archivoPdf);
                                    //echo 'el archivo no se pudo eliminar';
                                }
                            } else {
                                $mensaje = "Reservacion Exitosa. Error al enviar el correo.";
                                $tipo_mensaje = "cuidado";
                                $this->irAReservacion($mensaje,$tipo_mensaje,$mostrar_qr);
                                return;
                            }
                        }

                        // Mostrar mensaje de éxito y redirigir
                        $mensaje = "Reservación creada exitosamente. El comprobante fue enviado a su correo electronico";
                        $tipo_mensaje = "exito";
                        $this->irAReservacion($mensaje,$tipo_mensaje,$mostrar_qr);
                        return;
                    } catch (Exception $e) {
                        //echo "Error al generar el PDF: " . $e->getMessage();
                        $mensaje = "Reservación creada exitosamente. Error al generar el PDF";
                        $tipo_mensaje = "cuidado";
                        $this->irAReservacion($mensaje,$tipo_mensaje,$mostrar_qr);
                        return;
                    }
                }
            } else {
                //si no se encontro la parcela, entonces se buscara aquellas condiciones que no se pudieron cumplir
                //para que asi el usuario sepa porque razones no ha sido encontrada la parcela
               // echo "<script>console.log('" . addslashes("Llego a la parte de que fracaso la reservacion") . "');</script>";

                $fallas = $this->model->analizarFalloDeReserva($fecha_inicio, $fecha_fin, $cantPersonas, $fogon, $tomaElectrica, $sombra, $agua,$con_ducha,$tipo_de_vehiculo);
                //echo "<script>console.log('".addslashes("id servicio-> ".$fallas[0])."');</script>";

                if (!empty($fallas)) {
                    $mensaje = "<strong>No se pudo encontrar una parcela disponible, debido a: </strong><ul>";
                    foreach ($fallas as $falla) {
                        $mensaje .= "<h5>$falla</h5>";
                    }
                    $mensaje .= "</ul>";
                    $tipo_mensaje = "error";
                    $this->irAReservacion($mensaje,$tipo_mensaje,false);
                    return;
                }

                //en caso de que la funcion no encontro las fallas,pero no se pudo encontrar alguna para reservar
                //entonces por defecto se envia el siguiente mensaje
                $mensaje = "No se ha encontrado una parcela con las características indicadas.";
                $tipo_mensaje = "error";
                $this->irAReservacion($mensaje,$tipo_mensaje,false);
                return;
            }
        }
        //en caso de que no este logueado
        $this->irAReservacion();

    }
    public function confirmarReserva()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();

        if ($logueado && ($rol == 'admin' || 'super')) {
            $id_reserva = $_POST['id_reserva'];
            $id_del_usuario = $this->helper->getId();

            $usuario_login = $this->modelUser->findUserById($id_del_usuario);
            $nombre = $usuario_login['nombre'];
            $apellido = $usuario_login['apellido'];
            $dni = $usuario_login['dni'];

            $this->model->confirmarReservacion($id_reserva);
            $reservaciones = $this->model->getReservacionesMasUsuario();
            $this->view->ir_seccion_Reservacion($rol, $logueado, $id_parcela = null, null, null, BaseController::getDisponibilidad(), $nombre, $apellido, $dni, $reservaciones);
            return;
        }
        $this->irAReservacion();
    }
    /**
     * Funcion que permite al usuario cancelar aquella reservacion que le 
     * interese
     * ---notar que falta agregarle el logueo para que solo el usuario registrado
     * ---o el superadmin pueda acceder a el, y al usuario solo se le permitira 
     * ---cancelar sus reservas dentro de un plazo de tiempo
     */
    public function cancelarReserva()
    {
        // Verificar si se recibió por POST y si hay una sesión activa
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {

            $id_reserva = $_POST['id_reserva'];

            echo "<script>console.log('" . addslashes("id reserva-> " . $id_reserva) . "');</script>";

            // Primero eliminar la relación reserva-parcela
            $elimino_relacion = $this->model->eliminarRelacionParcelaReserva($id_reserva);

            if ($elimino_relacion) {
                // Luego eliminar la reserva
                $elimino_reserva = $this->model->eliminarReserva($id_reserva);

                if ($elimino_reserva) {
                    $_SESSION['mensaje_reserva'] = ['tipo' => 'success', 'texto' => 'La reservación fue eliminada con éxito.'];
                } else {
                    $_SESSION['mensaje_reserva'] = ['tipo' => 'danger', 'texto' => 'Error al eliminar la reservación.'];
                }
            } else {
                $_SESSION['mensaje_reserva'] = ['tipo' => 'warning', 'texto' => 'No se pudo eliminar la relación con la parcela. Intente más tarde.'];
            }
        } else {
            $_SESSION['mensaje_reserva'] = ['tipo' => 'danger', 'texto' => 'No se recibió una solicitud válida para cancelar la reserva.'];
        }

        // Redirigir a la página de reservas después de realizar la acción
        header("Location: " . BASE_URL . "seccion_reservacion");
        exit;
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
   private function getServicioAdicional($fogon, $tomaElectrica, $sombra,$con_ducha, $agua)
{
    // Si todas las características son 0, buscar la parcela más básica
    if ($fogon == 0 && $tomaElectrica == 0 && $sombra == 0 && $agua == 0 && $con_ducha == 0) {
        return $this->model->getParcelaBasica();
    }

    // Buscar el servicio con las características dadas
    $idServicio = $this->model->findServicio($fogon, $tomaElectrica, $sombra,$con_ducha, $agua);

    // Si no se encuentra el servicio, retornar `null`
    return !empty($idServicio) ? $idServicio : null;
}

    /**
     * Funcion que lleva a la pagina de reservacion que muestra el formulario de la reservacion
     */
    public function irAReservacion($mensaje=null,$tipo_mensaje=null,$mensaje_qr=false)
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $id_del_usuario = $this->helper->getId();
        //si no esta logueado se mostrara un cartel de que debe 
        //iniciar sesion para poder reservar
        if ($logueado) {
            $usuario_login = $this->modelUser->findUserById($id_del_usuario);
            //si encontro al usuario entonces se cargan las 3 variables con los
            //datos del usuario, para que el formulario al mostrarse ya tenga datos precargados
            if (!empty($usuario_login)) {
                $nombre = $usuario_login['nombre'];
                $apellido = $usuario_login['apellido'];
                $dni = $usuario_login['dni'];
                if ($rol == 'admin' || 'super') {
                    //en este caso se buscara todas las reservaciones mas los datos del usuario relevantes que 
                    //hizo la reservacion para que asi el admin pueda con esos datos confirmar o cancelar la reservacion
                    $reservaciones = $this->model->getReservacionesMasUsuario();
                    $this->view->ir_seccion_Reservacion($rol, $logueado, $id_parcela = null, $mensaje, $tipo_mensaje,$mensaje_qr, BaseController::getDisponibilidad(), $nombre, $apellido, $dni, $reservaciones);
                    return;
                }
                $this->view->ir_seccion_Reservacion($rol, $logueado, $id_parcela = null, $mensaje, $tipo_mensaje,$mensaje_qr, BaseController::getDisponibilidad(), $nombre, $apellido, $dni);
            } else {
                $this->view->ir_seccion_Reservacion($rol, $logueado, $id_parcela = null, $mensaje, $tipo_mensaje,$mensaje_qr, BaseController::getDisponibilidad());
            }
            //die();
        } else {
            $mensaje = "debera iniciar sesion para poder reservar parcelas";
            $this->view->seccionReservacionPublica($mensaje, BaseController::getDisponibilidad());
        }
    }
    /**
     * Funcion que lleva a la seccion de la pagina que muestra las preguntas mas frecuentes
     */
    public function preguntasFrec()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->pregFrec($rol, $logueado, BaseController::getDisponibilidad());
    }
    /**
     * Funcionalidad para editar los precios
     */
    public function editarPrecio()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();

        if ($rol === 'super' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar los datos enviados por el formulario
            $data = $_POST;

            // Validar las columnas permitidas
            $columnasPermitidas = [
                'edad_ninos4_no_residentes',
                'edad_ninos4_residentes',
                'edad_ninos12_no_residentes',
                'edad_ninos12_residentes',
                'edad_ninos20_no_residentes',
                'edad_ninos20_residentes',
                'costoxvehiculoxdia_no_residentes',
                'costoxvehiculoxdia_residentes',
                'costoxcasillaxdia_no_residentes',
                'costoxcasillaxdia_residentes',
                'costoxmescasilla_no_residentes',
                'costoxmescasilla_residentes',
                'costo_estancia_xdia_no_residentes',
                'costo_estancia_xdia_residentes',
                'costo_ducha_no_residentes',
                'costo_ducha_residentes',
                'costo_sanitario_no_residentes',
                'costo_sanitario_residentes'
            ];

            // Procesar cada columna enviada en el formulario
            foreach ($data as $columna => $valor) {
                if (in_array($columna, $columnasPermitidas)) {
                    // Identificar si es residente o no residente
                    $tipoResidente = strpos($columna, '_no_residentes') !== false ? 0 : 1;
                    $columnaBase = str_replace(['_no_residentes', '_residentes'], '', $columna);

                    // Actualizar la base de datos
                    $actualizado = $this->model->editarPrecio($columnaBase, $valor, $tipoResidente);

                    if (!$actualizado) {
                        $mensaje = "Error al actualizar la columna $columna.";
                        $this->authView->renderError($mensaje);
                        return;
                    }
                } else {
                    $mensaje = "Columna no válida: $columna.";
                    $this->authView->renderError($mensaje);
                    return;
                }
            }

            // Recargar la vista con los precios actualizados
            $precios = $this->model->getPrecioSLista(); // Obtiene nuevamente los precios divididos
            $this->view->renderPrecios($logueado, $rol, $precios, BaseController::getDisponibilidad());
        } else {
            // Manejo de acceso denegado
            $mensaje = "No tienes permisos para acceder a esta sección.";
            $this->authView->renderError($mensaje);
        }
    }
}
