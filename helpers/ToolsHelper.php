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

            // Configurar la salida del PDF
            $fileName = 'reserva' . $identificador . '.pdf';

            // Enviar encabezados para forzar la descarga
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="reserva_' . $identificador . '.pdf"');
            $pdf->Output('reserva_' . $identificador . '.pdf', 'D');
        } catch (Exception $e) {
            error_log("Error al generar el PDF: " . $e->getMessage());
        }
    }

    //funcion que genera un numero alfanumerico unico para identificar las reservaciones
    public function generarIdentificador()
    {
        return strtoupper(bin2hex(random_bytes(10)));
    }
}
