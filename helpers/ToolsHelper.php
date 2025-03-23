<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Incluir PHPMailer
/**
 * Este archivo contiene funciones que sus funcionalidades
 * pueden ser usadas por distintos archivos tales como
 * -->generar un documento pdf con la informacion dada por parametro
 * -->enviar un pdf a un email
 */
class ToolsHelper
{
    /**
     * nombre de la cuenta de gmail remitente
     */
    private $nombre_cuenta = 'mateo oscuro';
    /**
     * email de la cuenta de gmail remitente:
     */
    private $email_remitente = 'mateooscuro43@gmail.com';

    /**
     * //contraseña de la cuenta de gmail remitente:
     */
    private $password_remitente = 'xfmj dbla oxqk reaq ';

    /**
     * Genera un archivo pdf el cual su informacion es dada 
     * por los parametros que recibe la funcion
     * @param string $nombre nombre completo del usuario
     * @param string $apellido apellido del usuario
     * @param string $identificador numero alfanumerico que identifica la reserva
     * @param float $precio monto calculado de la reservacion
     * @param string $washapp numero de washapp en el cual en el mismo archivo pdf recomienda confirmar el pago
     * 
     * @return string
     */
    function generarPDF($nombre, $apellido, $identificador, $precio, $washapp)
    {
        try {
            // Limpiar cualquier salida previa
            if (ob_get_length()) {
                ob_clean();
            }
            // Crear el objeto TCPDF
            $pdf = new TCPDF();
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 16);

            // Título del archivo
            $pdf->Cell(0, 10, 'Comprobante de Reserva', 0, 1, 'C');
            $pdf->Ln(10);

            // Contenido
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 10, 'Nombre: ' . $nombre, 0, 1);
            $pdf->Cell(0, 10, 'Apellido: ' . $apellido, 0, 1);
            $pdf->Cell(0, 10, 'Precio estimado de dicha reserva: $' . $precio, 0, 1);
            $pdf->Cell(0, 10, 'Identificador: ' . $identificador, 0, 1);
            $pdf->Ln(10);
            $pdf->Cell(0, 10, 'Comuníquese al número: ' . $washapp . ' para confirmar su reserva.', 0, 1);
            $pdf->Ln(10);
            $pdf->MultiCell(0, 10, "Aviso: Si no confirma el medio de pago a través del número de WhatsApp proporcionado, su reservación se eliminará luego de 5 días de haberla pedido.");
            // Ruta donde se guardará el PDF
            $directory = __DIR__ . '/tmp/';
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            $fileName = 'reserva_' . $identificador . '.pdf';
            $filePath = $directory . $fileName;

            // Guardar el PDF en el servidor
            $pdf->Output($filePath, 'F');

            // Retornar la ruta completa del archivo
            return $filePath;
        } catch (Exception $e) {
            error_log("Error al generar el PDF: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Se encarga de enviar el archivo pdf a aquella direccion de email que recibe como parametro
     * @param string $email direccion email de aquel usuario que recibira el pdf
     * @param string $nombre nombre del usuario
     * @param string $rutaPDF direccion de donde se encuentra el archivo pdf para que lo pueda invocar
     * 
     * @return bool retorna un booleano que nos devuelve un true si la operacion se ejecuto o un false
     */
    function enviarCorreoConPDF($email, $nombre, $rutaPDF)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
            $mail->SMTPAuth = true;
            $mail->Username = $this->email_remitente; // Tu dirección de correo
            $mail->Password = $this->password_remitente; // Contraseña de la aplicación (ver más abajo)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configuración del correo
            $mail->setFrom($this->email_remitente, $this->nombre_cuenta);
            $mail->addAddress($email, $nombre);
            $mail->addAttachment($rutaPDF); // Adjuntar PDF
            $mail->isHTML(true);
            $mail->Subject = 'Comprobante de Reserva';
            $mail->Body = "Hola $nombre,<br><br>Gracias por realizar tu reserva. Adjuntamos tu comprobante en formato PDF.<br><br>Saludos,<br>Equipo de Reservas.";

            // Enviar correo
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar el correo: " . $mail->ErrorInfo);
            return false;
        }
    }
    /**Funcion que sirve para enviar un mensaje de mail para alertar a los usuarios de que
     * la reservacion finalizara*/
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
    /**
     *Funcion que genera un numero alfanumerico unico para identificar las reservaciones 
     */
    public function generarIdentificador()
    {
        return strtoupper(bin2hex(random_bytes(10)));
    }
}
