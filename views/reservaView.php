<?php

class ReservaView {
    public function showHome() {
       

require './templates/home.phtml';

    }

    public function pregFrec() {
       require './templates/preguntas.phtml';
    }
    
    public function reservacion() {
        require './templates/reservacion.phtml';
    }

    public function precios() {
        require './templates/precios.phtml';
    }
    public function mostrarParcelasDisponibles($reservaciones){
        require './templates/reservacion.phtml';
    }
    public function mostrarPrecioParcela($precio_final){
        require './templates/precios.phtml';
    }
}