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
    
        public function parcelas() {
        require './templates/parcelas.phtml';
     }
}
