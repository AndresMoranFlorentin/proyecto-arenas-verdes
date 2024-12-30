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

    public function pregFrec($logueado, $rol) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/preguntas.phtml';
     }
     
     public function reservacion($rol, $logueado) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/reservacion.phtml';
     }
     public function mostrarParcelasDisponibles($reservaciones){
        require './templates/reservacion.phtml';
    }
    public function mostrarPrecioParcela($precio_final){
        require './templates/precios.phtml';
    }
}
