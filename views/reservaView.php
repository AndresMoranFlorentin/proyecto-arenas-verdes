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
    public function seccionReservacionPublica($mensaje,$dispo){
        require './templates/reservacion_publica.phtml';
    }
    public function ir_seccion_Reservacion($rol,$logueado,$id_parcela=null,$mensaje=null,$tipo_mensaje=null,$dispo,$nombre=null,$apellido=null,$dni=null){
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
}
