<?php

class ReservaView
{
    private $logueado;
    private $rol;

    public function showHome($logueado)
    {

        $this->logueado = $logueado;
        $this->rol = 'admin';
        require './templates/home.phtml';
    }

    public function renderPrecios($logueado, $rol){
        $this->logueado = $logueado;
        $this->rol = 'admin';
        require './templates/precios.phtml';
    }
}
