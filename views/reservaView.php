<?php

class ReservaView
{
    private $logueado;
    private $rol;
    private $mensaje="";
    private $tipo_mensaje="";
    private $urlPdf="";
    private $precios;

    public function showHome($logueado, $rol,$dispo)
    {

        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/home.phtml';
    }

    public function renderPrecios($logueado, $rol, $precios, $dispo){
        $this->logueado = $logueado;
        $this->rol = $rol;
        $this->precios = $precios;
        require './templates/precios.phtml';
    }
     
    
    public function pregFrec($rol, $logueado, $dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/preguntas.phtml';
     }
     public function mostrarParcela($rol, $logueado, $dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/parcelas.phtml';
     }
     public function mostrarParcelasDisponibles($rol, $logueado,$reservaciones,$parcelas_por_sector,$fechaInicio,$fechaFin,$dispo){
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/parcelas.phtml';
    }
    public function mostrarPrecioParcela($rol, $logueado,$precio_final=null,$precios,$dispo){
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/precios.phtml';
    }
    public function ir_seccion_Reservacion($rol,$logueado,$id_parcela=null,$mensaje=null,$tipo_mensaje=null,$dispo,){
        $this->mensaje="";
        $this->tipo_mensaje="";
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/reservacion.phtml';
    }
    public function irSeccionAMisReservas($rol, $logueado,$dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/mis_reservas.phtml';
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
