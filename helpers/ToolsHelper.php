<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use PHPMailer\PHPMailer\Exception;
// Incluir PHPMailer
require 'vendor/autoload.php'; // Si usas Composer
class ToolsHelper
{
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
   
function enviarCorreoConPDF($email, $nombre, $rutaPDF)
{
    require 'vendor/autoload.php';

    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'mateooscuro43@gmail.com'; // Tu dirección de correo
        $mail->Password = 'xfmj dbla oxqk reaq ';// Contraseña de la aplicación (ver más abajo)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('mateooscuro43@gmail.com', 'mateo oscuro');
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

    //funcion que genera un numero alfanumerico unico para identificar las reservaciones
    public function generarIdentificador()
    {
        return strtoupper(bin2hex(random_bytes(10)));
    }
}
