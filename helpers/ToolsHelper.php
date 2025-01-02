<?php
require_once 'vendor/autoload.php';

class ToolsHelper
{
    function generarPDF($nombre, $apellido, $identificador,$precio,$washapp) {
        if (ob_get_length()) {
            ob_end_clean();
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
        $pdf->Cell(0, 10, 'Precio estimado de dicha reserva: ' . $precio, 0, 1,' $ ');
        $pdf->Cell(0, 10, 'Identificador: ' . $identificador, 0, 1);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Comuniquese al numero : ' . $washapp, 0, 1,' para poder confirmar su reserva ');
        $pdf->Ln(10);
        $pdf->MultiCell(0, 10, "Aviso: Si no confirma el medio de pago a través del número de WhatsApp proporcionado, su reservación se eliminará luego de 5 días de haberla pedido.");
         // Nombre del archivo
         $fileName = 'reserva_' . $identificador . '.pdf';

         // Guardar temporalmente (opcional)
         $tempDir = sys_get_temp_dir(); // Carpeta temporal
         $tempPath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
 
         $pdf->Output($tempPath, 'F'); // Guardar temporalmente
 
         // Forzar la descarga del archivo
         header('Content-Type: application/pdf');
         header('Content-Disposition: attachment; filename="' . $fileName . '"');
         header('Content-Length: ' . filesize($tempPath));
         readfile($tempPath);
 
         // Eliminar archivo temporal después de la descarga
         unlink($tempPath);
     }
     //funcion que genera un numero alfanumerico unico para identificar las reservaciones
     public function generarIdentificador() {
        return strtoupper(bin2hex(random_bytes(10)));
    }
}
