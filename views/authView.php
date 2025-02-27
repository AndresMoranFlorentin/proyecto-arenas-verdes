<?php 

class AuthView {

    private $logueado;
    private $rol;
    private $users;
    private $error;

    public function showLogin($error = null) {
        $this->error = $error;
        require './templates/login.phtml';
    }

    public function showRegisForm($error = null) {
        $this->error = $error;
        require './templates/form.phtml';
    }

    public function renderError($error = null){
        $this->error = $error;
        require './templates/error.phtml';

    }

    public function renderHome($logueado, $rol,$dispo=true){
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/home.phtml';
    }

    public function renderUsers($users, $logueado, $rol){
        $this->logueado = $logueado;
        $this->rol = $rol;
        $this->users = $users;
        require './templates/users.phtml';
    }
}