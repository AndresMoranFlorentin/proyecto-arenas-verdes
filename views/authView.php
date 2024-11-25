<?php 

class AuthView {
    public function showLogin($error = null) {
        require './templates/login.phtml';
    }

    public function renderError($error = null){

    }

    public function renderHome($error = null){
        require './templates/home.phtml';
    }
}