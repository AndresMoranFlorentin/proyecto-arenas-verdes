<?php 

class AuthView {

    private $logueado;
    private $rol;

    public function showLogin($error = null) {
        require './templates/login.phtml';
    }

    public function showRegisForm($error = null) {
        require './templates/form.phtml';
    }

    public function renderError($error = null){

    }

    public function renderHome($logueado, $rol){
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/home.phtml';
    }
}