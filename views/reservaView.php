<?php

class ReservaView
{
    private $logueado;
    public function showHome($logueado)
    {

        $this->logueado = $logueado;
        require './templates/home.phtml';
    }

    public function renderPrecios($logueado){
        $this->logueado = $logueado;
        require './templates/precios.phtml';
    }
}
