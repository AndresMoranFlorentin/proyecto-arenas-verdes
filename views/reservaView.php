<?php

class ReservaView
{
    private $logueado;
    private $rol;

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
     public function mostrarParcelasDisponibles($reservaciones){
        require './templates/reservacion.phtml';
    }
    public function mostrarPrecioParcela($precio_final){
        require './templates/precios.phtml';
    }
    public function formSolicitarReservacion($id_parcela=null){
        $mensaje="La reservacion fue hecha con exito";
        $tipo_mensaje="exito";
        require './templates/formParaReservacion.phtml';
        
    }
    public function mostrarFormularioReservacion($mensaje, $tipo_mensaje){
        var_dump("mensaje: ".$mensaje.", tipo : ".$tipo_mensaje);
        require './templates/formParaReservacion.phtml';
    }
}
