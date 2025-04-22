<?php

class ReservaView
{
    private $logueado;
    private $rol;
    private $mensaje="";
    private $tipo_mensaje="";
    private $urlPdf="";

    public function showHome($logueado, $rol,$dispo)
    {

        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/home.phtml';
    }

    public function renderPrecios($logueado, $rol, $dispo){
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/precios.phtml';
    }
     
    
    public function pregFrec($rol, $logueado, $dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/preguntas.phtml';
     }
     
     public function reservacion($rol, $logueado, $dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/reservacion.phtml';
     }
     public function mostrarParcelasDisponibles($reservaciones,$parcelas_por_sector,$fechaInicio,$fechaFin,$dispo){
        require './templates/reservacion.phtml';
    }
    public function mostrarPrecioParcela($precio_final,$dispo){
        require './templates/precios.phtml';
    }
    public function formSolicitarReservacion($id_parcela=null,$dispo){
        $this->mensaje="";
        $this->tipo_mensaje="";
        require './templates/formParaReservacion.phtml';
    }
    public function mostrarFormularioReservacion($mensaje, $tipo_mensaje,$dispo) {
        require './templates/formParaReservacion.phtml';
    }
public function mostrarReservas($reservas)
{
    if (!empty($reservas)) {
        foreach ($reservas as $reserva) {
            echo "<div class='reserva d-flex justify-content-between align-items-center p-2 border rounded mb-2'>";
            
            echo "<div class='me-3'>";
            echo "<p class='mb-0'>Reserva ID: {$reserva['id']} - Fecha inicio: {$reserva['fecha_inicio']} - Fecha fin: {$reserva['fecha_fin']}</p>";
            echo "</div>";

            // Formulario que llama a cancelarReserva() usando POST
            echo "<form method='POST' action='' class='m-0'>";
            echo "<input type='hidden' name='id_reserva' value='{$reserva['id']}'>";
            echo "<button type='submit' name='eliminar_reserva' class='btn btn-outline-danger btn-sm' title='Eliminar'>";
            echo "<i class='bi bi-trash'></i>";
            echo "</button>";
            echo "</form>";

            echo "</div>";
        }
    } else {
        echo "<p>No hay reservas para este usuario.</p>";
    }
}



}
