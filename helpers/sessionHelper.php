<?php

class SessionHelper
{
    /**
     * Constructor: Inicia la sesión si no está activa.
     */
    function __construct()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Inicia sesión para un usuario autenticado.
     * Guarda el estado de sesión, rol e ID del usuario.
     *
     * @param object $user Objeto del usuario con propiedades 'rol' e 'id'.
     */
    function logIn($user)
    {
        if (!$this->sessionVerify()) {
            session_start();
        }
        $_SESSION["logueado"] = true;
        $_SESSION["rol"] = $user->rol;
        $_SESSION["id"] = $user->id;
        
    }

    /**
     * Obtiene el rol del usuario autenticado.
     *
     * @return string|null Retorna el rol del usuario o null si no está autenticado.
     */
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

    /**
     * Obtiene el ID del usuario autenticado.
     *
     * @return int|null Retorna el ID del usuario o null si no está autenticado.
     */
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

    /**
     * Verifica si la sesión está activa.
     *
     * @return bool Retorna true si la sesión está activa, false en caso contrario.
     */
    function sessionVerify()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            return true;
        }
        return false;
    }

    /**
     * Verifica si el usuario está autenticado.
     *
     * @return bool Retorna true si el usuario está autenticado, false en caso contrario.
     */
    function checkUser()
    {
        if ($this->sessionVerify()) {
            if (isset($_SESSION["logueado"]) && $_SESSION["logueado"]) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cierra la sesión del usuario autenticado.
     * Destruye la sesión si está activa.
     */
    function logOut()
    {
        if ($this->sessionVerify()) {
            session_destroy();
        }
    }
}
