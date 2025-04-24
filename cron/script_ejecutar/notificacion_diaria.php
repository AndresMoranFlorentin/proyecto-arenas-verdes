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

//Datos del remitente (pueden cambiarse por otros):
//nombre de la cuenta:
$nombre_cuenta = 'mateo oscuro';
//email:
$email_remitente = 'mateooscuro43@gmail.com';
//contraseña:
$password_remitente = 'xfmj dbla oxqk reaq ';


//instancia de la clase modelo
$modelo = new ReservaModel();
//instancia de la clase tool helper quien tiene las funciones de email
$toolHelper= new ToolsHelper();
//trae todas las notificaciones que todavia no se enviaron
//y que ya cumplen la fecha de vencimiento
$notificaciones = $modelo->getNotificaciones();
//en esta variable(que se deberia mantener fija)esta el numero
//de parcelas que se considera baja disponibilidad
$num_min_parce_dispo = 3;

//se recorren todas aquellas notificaciones encontradas en la fecha actual
if (isset($notificaciones)) {
    foreach ($notificaciones as $noti) {
        $idNot = $noti["id"];
        $nombre = $noti["nombre_completo"];
        $email = $noti["email"];
        echo "| id: ".$idNot." | ";
        $respuesta =$toolHelper->enviarEmail($nombre, $email);
        if ($respuesta) { //en caso de que la notificacion fue enviada con exito..
            //elimino la notificacion ya que no es necesaria conservarla
            $modelo->deleteNotificacion($idNot);
        }
    }
}
//en esta seccion se consulta a la base de datos la disponibilidad de parcelas
//se busca enm la bbdd si hay disponibilidad de parcelas o no
$disponibilidad = $modelo->hayDisponibilidad($num_min_parce_dispo);

if ($disponibilidad) { //en caso de que haya disponibilidad
    //lo sigue mostrando en true
    BaseController::setDisponibilidad(true);
    echo "disponibilidad->|true |";
    echo "ahora esta en -> ",BaseController::getDisponibilidad();
    }
else { //en caso de que haya baja disponibilidad
     //se seteara con false la variable de disponibilidad
    //para que se muestre el cartel
    BaseController::setDisponibilidad(false);
    echo "disponibilidad->|false |";
    echo "ahora esta en -> ",BaseController::getDisponibilidad();
    }

