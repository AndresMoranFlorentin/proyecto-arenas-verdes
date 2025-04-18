<?php

/**
 * este archivo fue creado con el proposito de ejecutarse una vez
 * todos los dias, para avisar por medio de gmail a aquellos usuarios 
 * cuya reservacion este por vencerse en la fecha actual
 * tambien se le añade la funcion de que debe alertar en la
 * pagina(mediante un cartel flotante) en caso
 * de baja disponibilidad de reservas al usuario
 */

require '../vendor/autoload.php';
//require_once '../vendor/autoload.php';
require_once '../models/ReservaModel.php';
//require_once '../controllers/ReservaController.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Función para escribir en el archivo de registro

function writeLog($message)
{
    $logFile = __DIR__ . '/log_cron.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// Escribimos el inicio del script en el log
writeLog(' Inicio de la ejecución del script de notificación diaria.');


//Datos del remitente (pueden cambiarse por otros):
//nombre de la cuenta:
$nombre_cuenta = 'mateo oscuro';
//email:
$email_remitente = 'mateooscuro43@gmail.com';
//contraseña:
$password_remitente = 'xfmj dbla oxqk reaq ';
//instancia de la clase modelo
$modelo = new ReservaModel();
//en esta variable(que se deberia mantener fija)esta el numero
//de parcelas que se considera baja disponibilidad
$num_min_parce_dispo = 4;
$disponibilidad = $modelo->hayDisponibilidad($num_min_parce_dispo);

$dispo =mostrarCartel($disponibilidad);
writeLog('disponibilidad etapa 1 ?: ' . print_r($notificaciones));

//trae todas las notificaciones que todavia no se enviaron
//y que ya cumplen la fecha de vencimiento
$notificaciones = $modelo->getNotificaciones();
writeLog('notificaciones?: ' . print_r($notificaciones));

//se recorren todas aquellas notificaciones encontradas en la fecha actual
if (!isset($notificaciones)) {
    foreach ($notificaciones as $noti) {
        $idNot = $noti["id"];
        $nombre = $noti["nombre_completo"];
        $email = $noti["email"];
        $respuesta = enviarEmail($nombre, $email);
        if ($respuesta) { //en caso de que la notificacion fue enviada con exito..
            //elimino la notificacion ya que no es necesaria conservarla
            $modelo->deleteNotificacion($idNot);
        }
    }
}
//en esta seccion se consulta a la base de datos la disponibilidad de parcelas

function mostrarCartel($disponibilidad)
{

    writeLog('hay disponibilidad?: ' . print_r($disponibilidad));

    if ($disponibilidad) { //en caso de que haya disponibilidad
        //lo sigue mostrando en true
        ReservaController::setDisponibilidad(true);
    } else { //en caso de que haya baja disponibilidad
        //se seteara con false la variable de disponibilidad
        //para que se muestre el cartel
        ReservaController::setDisponibilidad(false);
    }
}
//funcion que sirve para enviar un mensaje de mail para alertar a los usuarios de que
//la reservacion finalizara
function enviarEmail($nombre_destinatario, $email_destinatario)
{
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mateooscuro43@gmail.com'; // el correo Gmail remitente
        $mail->Password = 'xfmj dbla oxqk reaq '; // contraseña del remitente
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinatarios
        $mail->setFrom('mateooscuro43@gmail.com', 'mateo oscuro');
        $mail->addAddress($email_destinatario, $nombre_destinatario); // Añadir destinatario

        // Contenido del correo
        $mail->isHTML(true); // Habilitar HTML
        $mail->Subject = 'Recordatorio de Reservación';
        $mail->Body    = '<p>Hola,</p>' . $nombre_destinatario . '<p>Te recordamos que tu reservación termina <strong>mañana</strong>. ¡Gracias por elegirnos!</p>';
        $mail->AltBody = 'Hola, Te recordamos que tu reservación termina mañana. ¡Gracias por elegirnos!';

        // Enviar correo
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
