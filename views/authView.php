<?php 

class AuthView {

    private $logueado;
    private $rol;
    private $users;
    private $error;
    private $user;
    private $token;

    

    

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

    public function renderPerfil($user, $logueado, $rol ){
        $this->user = $user;
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/perfil.phtml';
    }

    public function renderPassForm($token){
        $this->token = $token;
            require './templates/resetPass.phtml';
    }
}