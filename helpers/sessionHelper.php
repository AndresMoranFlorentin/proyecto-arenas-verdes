<?php

class SessionHelper
{

    function __construct()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    function iniciaSesion($user)
    {
        if (!$this->sessionVerify()) {
            session_start();
        }
        $_SESSION["logueado"] = true;
        $_SESSION["rol"] = $user->rol;
        $_SESSION["id"] = $user->id_usuario;
        
    }

    function getRol()
    {
        if (!$this->sessionVerify()) {
            session_start();
        }
        if (isset($_SESSION["rol"])){
            return $_SESSION["rol"];
        }else {
            return null;
        }
    }

    function getId()
    {
        if (!$this->sessionVerify()) {
            session_start();
        }
        if (isset($_SESSION["id"])) {
            $id = $_SESSION["id"];
            return $id;
        } else {
            return null;
        }
    }


    function sessionVerify()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            return true;
        }
        return false;
    }

    function checkUser()
    {
        if ($this->sessionVerify()) {
            if (isset($_SESSION["logueado"]) && $_SESSION["logueado"]) {
                return true;
            }
        }
        return false;
    }


    function cerrarSesion()
    {
        if ($this->sessionVerify()) {
            session_destroy();
        }
    }
}
