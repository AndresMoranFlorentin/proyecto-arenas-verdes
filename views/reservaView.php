<?php

class ReservaView
{
    private $logueado;
    private $rol;
    private $mensaje="";
    private $tipo_mensaje="";
    private $urlPdf="";

    public function showHome($logueado, $rol)
    {

        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/home.phtml';
    }

    public function renderPrecios($logueado, $rol){
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/precios.phtml';
    }

    public function pregFrec() {
        require './templates/preguntas.phtml';
     }
     
     public function reservacion() {
         require './templates/reservacion.phtml';
     }
     public function mostrarParcelasDisponibles($reservaciones,$parcelas_por_sector,$fechaInicio,$fechaFin){
        require './templates/reservacion.phtml';
    }
    public function mostrarPrecioParcela($precio_final){
        require './templates/precios.phtml';
    }
    public function formSolicitarReservacion($id_parcela=null){
        $this->mensaje="";
        $this->tipo_mensaje="";
        require './templates/formParaReservacion.phtml';
        
    }
    public function mostrarFormularioReservacion($mensaje, $tipo_mensaje) {
        $this->mensaje=$mensaje;
        $this->tipo_mensaje=$tipo_mensaje;
        require './templates/formParaReservacion.phtml';
    }
}
