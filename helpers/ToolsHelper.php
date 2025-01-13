<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Incluir PHPMailer
require 'vendor/autoload.php'; // Si usas Composer
class ToolsHelper
{
    function generarPDF($nombre, $apellido, $identificador, $precio, $washapp)
    {
        try {
            // Crear el objeto TCPDF
            $pdf = new TCPDF();
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 16);

            // Título
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

            // Nombre del archivo PDF
            $fileName = 'reserva_' . $identificador . '.pdf';
            // Limpiar cualquier salida previa
            ob_clean();
            // Enviar el archivo como descarga al navegador
            $pdf->Output($fileName, 'D');

            // Terminar el script después de la descarga
            exit();
        } catch (Exception $e) {
            error_log("Error al generar el PDF: " . $e->getMessage());
            return false;
        }
    }
    //funcion que genera un numero alfanumerico unico para identificar las reservaciones
    public function generarIdentificador()
    {
        return strtoupper(bin2hex(random_bytes(10)));
    }
    //funcion que sirve para enviar un mensaje de mail para alertar a los usuarios de que
    //la reservacion finalizara
    public function enviarEmail()
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tucorreo@gmail.com'; // Tu correo Gmail
            $mail->Password = 'tu_contraseña'; // Tu contraseña (o App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinatarios
            $mail->setFrom('tucorreo@gmail.com', 'Tu Nombre o Nombre del Negocio');
            $mail->addAddress('destinatario@example.com', 'Nombre del Destinatario'); // Añadir destinatario

            // Contenido del correo
            $mail->isHTML(true); // Habilitar HTML
            $mail->Subject = 'Recordatorio de Reservación';
            $mail->Body    = '<p>Hola,</p><p>Te recordamos que tu reservación termina <strong>mañana</strong>. ¡Gracias por elegirnos!</p>';
            $mail->AltBody = 'Hola, Te recordamos que tu reservación termina mañana. ¡Gracias por elegirnos!';

            // Enviar correo
            $mail->send();
            echo 'Correo enviado exitosamente';
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    }
}
