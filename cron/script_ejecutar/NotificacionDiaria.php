<?php
/**
 * este archivo fue creado con el proposito de ejecutarse una vez
 * todos los dias, para avisar por medio de gmail a aquellos usuarios 
 * cuya reservacion este por vencerse en la fecha actual
 * tambien se le añade la funcion de que debe alertar en la
 * pagina(mediante un cartel flotante) en caso
 * de baja disponibilidad de reservas al usuario
 */
require_once __DIR__ . '/../../models/ReservaModel.php';
require_once __DIR__ . '/../../helpers/ToolsHelper.php';
require_once __DIR__ . '/../../controllers/BaseController.php'; // Incluir el controlador

class NotificacionDiaria {
//Datos del remitente (pueden cambiarse por otros):
//nombre de la cuenta:
private $nombre_cuenta = 'reservasbasedecampamento';
//email:
private $email_remitente = 'reservasbasedecampamento@gmail.com';
//contraseña:
//private $password_remitente = 'xfmj dbla oxqk reaq ';
private $password_remitente = 'coiv ggaf vnha whts';
//en esta variable(que se deberia mantener fija)esta el numero
//de parcelas que se considera baja disponibilidad
private $num_min_parce_dispo = 15;
//permite saber si hay disponibilidad de reservas o no
private $disponibilidad=true;
//instancia de la clase modelo
private $modelo;
//instancia de la clase tool helper quien tiene las funciones de email
private $toolHelper;
function __construct()
    {
        $this->modelo = new ReservaModel();
        $this->toolHelper= new ToolsHelper();
    }
/**
 * Ejecuta tareas diarias si ha pasado al menos un minuto desde la última ejecución.
 *
 * Este método verifica la última fecha de ejecución registrada en la base de datos.
 * Si ha transcurrido al menos un minuto, una hora o un día desde entonces,
 * se ejecutan las siguientes tareas:
 *   - Envío de notificaciones por fin de reservación.
 *   - Actualización de disponibilidad.
 * Finalmente, se actualiza la fecha de última ejecución en la base de datos.
 *
 * @return void
 */

public function ejecutarTareasDiarias() {
    // Obtiene la última fecha de ejecución desde la base de datos (tabla config).
    $ultimaEjecucion = $this->modelo->getUltimaEjecucion(); // SELECT ultima_fecha FROM config

    // Crea objetos DateTime para la fecha actual y la última ejecución.
    $ahora = new DateTime();
    $ultima = new DateTime($ultimaEjecucion);

    $diff = $ahora->diff($ultima);
    // Si ha pasado al menos 1 minuto, 1 hora o 1 día, se ejecutan las tareas.
    //if ($diff->i >= 1 || $diff->h > 0 || $diff->days > 0 ) {
    // Ejecutar solo si han pasado al menos 4 horas
    if ($diff->h >= 4 || $diff->days > 0) {
        $this->avisoDeNotificacionesFinReservacion();
        $this->actualizar_disponibilidad();
        $this->modelo->setUltimaEjecucion($ahora->format("Y-m-d H:i:s"));
    }
}
//trae todas las notificaciones que todavia no se enviaron
//y que ya cumplen la fecha de vencimiento
public function avisoDeNotificacionesFinReservacion(){
    $notificaciones = $this->modelo->getNotificaciones();

    //se recorren todas aquellas notificaciones encontradas en la fecha actual
    if (isset($notificaciones)) {
        foreach ($notificaciones as $noti) {
            $idNot = $noti["id"];
            $nombre = $noti["nombre_completo"];
            $email = $noti["email"];
            //echo "<script>console.log('" . addslashes("Las siguientes id notificaciones: ".$idNot." | ") . "');</script>";
            $respuesta =$this->toolHelper->enviarEmail($nombre, $email);
            if ($respuesta) { //en caso de que la notificacion fue enviada con exito..
                //elimino la notificacion ya que no es necesaria conservarla
                $this->modelo->deleteNotificacion($idNot);
            }
        }
    }
   
}

 //en esta seccion se consulta a la base de datos la disponibilidad de parcelas
    //se busca enm la bbdd si hay disponibilidad de parcelas o no
public function actualizar_disponibilidad(){

$this->disponibilidad = $this->modelo->hayDisponibilidad($this->num_min_parce_dispo);

if ($this->disponibilidad) { //en caso de que haya disponibilidad
    //lo sigue mostrando en true
    BaseController::setDisponibilidad(true);
    }
else { //en caso de que haya baja disponibilidad
     //se seteara con false la variable de disponibilidad
    //para que se muestre el cartel
    BaseController::setDisponibilidad(false);
    }
}
}


