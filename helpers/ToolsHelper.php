<?php
require __DIR__ . '/../vendor/autoload.php';



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Ruta a FPDF (asegurate de que helpers/fpdf/fpdf.php exista)
require_once __DIR__ . '/fpdf/fpdf.php'; 
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
    private $nombre_cuenta;
    /**
     * email de la cuenta de gmail remitente:
     */
    private $email_remitente;

    /**
     * //contraseña de la cuenta de gmail remitente:
     */
    private $password_remitente;

    public function __construct()
    {
        $this->nombre_cuenta = $_ENV['MAIL_NAME'];
        $this->email_remitente = $_ENV['MAIL_USERNAME'];
        $this->password_remitente = $_ENV['MAIL_PASSWORD'];
    }
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
    public function generarPDF($nombre, $apellido, $identificador, $precio, $washapp)
    {
        try {
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'Comprobante de Reserva', 0, 1, 'C');
            $pdf->Ln(10);

            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, mb_convert_encoding('Nombre: ' . $nombre, 'ISO-8859-1', 'UTF-8'), 0, 1);
            $pdf->Cell(0, 10,mb_convert_encoding( 'Apellido: '. $apellido, 'ISO-8859-1', 'UTF-8'), 0, 1);
            $pdf->Cell(0, 10,mb_convert_encoding( 'Precio: $'. $precio, 'ISO-8859-1', 'UTF-8') , 0, 1);//"Precio estimado: $$precio"
            $pdf->Cell(0, 10, mb_convert_encoding( 'Identificador: '. $identificador, 'ISO-8859-1', 'UTF-8'), 0, 1);
            $pdf->Ln(10);
            $pdf->MultiCell(0, 10, mb_convert_encoding('Confirme su pago al número: ' . $washapp, 'ISO-8859-1', 'UTF-8'));
            $pdf->Ln(5);
            $pdf->MultiCell(0, 10, mb_convert_encoding('También puede escanear el siguiente código QR para transferir:', 'ISO-8859-1', 'UTF-8'));
            $pdf->Ln(5);
            // ⬇ QR centrado en la hoja
            $pdf->Image(__DIR__ . '/../img/qr/codigo_qr.png', 65, $pdf->GetY(), 80);
            $pdf->Ln(85); // Dejar espacio después del QR
            $pdf->MultiCell(0, 10, mb_convert_encoding('Aviso: Si no confirma el pago en 5 días, la reserva será cancelada.', 'ISO-8859-1', 'UTF-8'));
           
            $directory = __DIR__ . '/tmp/';
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $fileName = 'reserva_' . $identificador . '.pdf';
            $filePath = $directory . $fileName;
            $pdf->Output('F', $filePath);

            return $filePath;
        } catch (Exception $e) {
            error_log("Error al generar PDF: " . $e->getMessage());
            return false;
        }
    }

    public function enviarCorreoConPDF($email, $nombre, $rutaPDF)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST']; // Servidor SMTP de Gmail
            $mail->SMTPAuth = true;
            $mail->Username = $this->email_remitente;
            $mail->Password = $this->password_remitente;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($this->email_remitente, $this->nombre_cuenta);
            $mail->addAddress($email, $nombre);
            $mail->addAttachment($rutaPDF);
            $mail->isHTML(true);
            $mail->Subject = 'Comprobante de Reserva';
            $mail->Body = "Hola $nombre,<br><br>Gracias por realizar tu reserva. Adjuntamos tu comprobante en formato PDF.<br><br>Saludos,<br>Equipo de Reservas.";

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
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->email_remitente; // el correo Gmail remitente
            $mail->Password = $this->password_remitente; // contraseña del remitente
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'];

            // Destinatarios
            $mail->setFrom($this->email_remitente, $this->nombre_cuenta);
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
   /**Funcion que sirve para enviar un mensaje de mail para alertar al usuario de que
     * su reservacion ha finalizado porque la parcela ha sido inhabilitada abruptamente*/
    function notificarCancelacionReservaEmail($nombre_destinatario, $email_destinatario)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->email_remitente; // el correo Gmail remitente
            $mail->Password = $this->password_remitente; // contraseña del remitente
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'];

            // Destinatarios
            $mail->setFrom($this->email_remitente, $this->nombre_cuenta);
            $mail->addAddress($email_destinatario, $nombre_destinatario); // Añadir destinatario

            // Contenido del correo
            $mail->isHTML(true); // Habilitar HTML
            $mail->Subject = 'Aviso!!! su reservacion ha sido cancelada';
            $mail->Body    = '<p>Hola,</p>' . $nombre_destinatario . '<p> debido a que la parcela <strong> no esta en condiciones de ser usada </strong>, lamentamos informarle que tu reservación ha sido <strong>cancelada</strong>. Pedimos disculpas por la situacion</p>';
            $mail->AltBody = 'Hola, Le recordamos que tu reservación ha sido cancelada debido a que la parcela no esta disponible para su uso. ¡Pedimos disculpas por lo sucedido!';

            // Enviar correo 
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}