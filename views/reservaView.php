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
     
     public function reservacion($rol, $logueado, $dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/reservacion.phtml';
     }
     public function mostrarParcela($rol, $logueado, $dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/parcelas.phtml';
     }
     public function mostrarParcelasDisponibles($reservaciones,$parcelas_por_sector,$fechaInicio,$fechaFin,$dispo){
        //require './templates/reservacion.phtml';
        require './templates/parcelas.phtml';
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
        require './templates/reservacion.phtml';
    }
}
