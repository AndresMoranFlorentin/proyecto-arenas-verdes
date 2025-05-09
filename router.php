<?php

/**
 * router.php
 * 
 * Este archivo gestiona el enrutamiento de la aplicación web. 
 * Se encarga de dirigir las solicitudes entrantes a los controladores correspondientes
 * y ejecutar las acciones definidas.
 */

// Requiere los controladores necesarios para gestionar las acciones
require_once './controllers/ReservaController.php';
require_once './controllers/authController.php';
require_once './controllers/ParcelaController.php';
require_once './controllers/resetPassController.php';
require_once './controllers/InformeController.php';

// Define la URL base de la aplicación
// Ejemplo: http://localhost:8080/miApp/
define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

// Establece la acción por defecto a 'home'
$action = 'home';

// Si hay una acción especificada en la URL, la utiliza
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}
// Divide la acción en parámetros utilizando '/' como delimitador

$reservaController = new ReservaController();
$authController = new AuthController();
$passController = new PassResetController();
$parcelaController = new ParcelaController();
$informeController = new InformeController(); // Instancia del controlador de informes

$params = explode('/', $action);

// Enrutamiento de las acciones
switch ($params[0]) {

    /**
     * --- Funciones del ReservaController ---
     */
    case 'home' :
        // Muestra la página principal
        $reservaController->showHome();
        break;

    case 'precios' :
        // Muestra la página de precios;
        $reservaController->renderPrecios();
        break;
    case 'editarPrecios' :
        // Edita el precio de una parcela)
        $reservaController->editarPrecio();
        break;

    case 'simular_precios' :
        // Simula el precio de una reserva
        $reservaController->simularPrecioReserva();
        break;

    case 'buscarParcelasDispo' :
        // Busca las parcelas disponibles para reservar
        $reservaController->buscarParcelasDispo();
        break;
    /**********************************
     * * SECCION DE GENERAR RESERVACION
     *********************************/
    case 'seccion_reservacion' :
        // Solicita una reservación
        $reservaController->irAReservacion();
        break;
    case 'generar_reservacion' :
        // Genera una nueva reservación
        $reservaController->generarReservacion();
        break;
    case 'cancelar_reserva' :
            $reservaController->cancelarReserva();
        break;
    case 'confirmar_reserva' :
            $reservaController->confirmarReserva();
        break;
    case 'parcelas' :
        // Muestra las parcelas disponibles       
        $parcelaController->sectoresParcelas();
        break;
    case 'preguntas' :
        // Muestra las preguntas frecuentes
        $reservaController->preguntasFrec();
        break;
    case 'crt_parcelas' :
        // Muestra la sección de parcelas
        $parcelaController->seccionParcelas();
        break;
    case 'generar_informes' :
        $informeController->mostrarVistaInformes();
        break;
    case 'habilitar' :
        // Habilita una parcela
        $parcelaController->habilitarParcela();
        break;
    case 'inhabilitar' :
        // Inhabilita una parcela
        $parcelaController->inhabilitarParcela();
        break;
    /**
     * --- Funciones del AuthController ---
     */

    //*****AUTH CONTROLLER **************  

    case 'auth' :
        // Procesa la autenticación del usuario
        $authController->auth();
        break;
    case 'register' :
        // Registra un nuevo usuario
        $authController->register();
        break;
    case 'logout' :
        // Cierra la sesión del usuario
        $authController->logout();
        break;

    case 'users' :
        // Muestra la lista de usuarios
        $authController->getUsers();
        break;

    case 'editRol' :
        // Edita el rol de un usuario (requiere un parámetro adicional)
        $authController->editRol($params[1]);
        break;
    case 'deleteUser' :
        // Elimina un usuario (requiere un parámetro adicional)
        $authController->deleteUser($params[1]);
        break;
    case 'perfil' :
        $authController->verPerfil();
        break;
    case 'editarUser' :
        $authController->editarUser();
        break;
    case 'resetPassword' :
        $passController->resetPassword();
        break;
    case 'olvideContraseña' :
        $passController->sendRecoveryEmail();
        break;
    case 'newPassword' :
        $passController->showResetForm($params[1]);
        break;

    /**
     * --- Acción por defecto si no se encuentra la ruta ---
     */
    default :
        // Muestra un mensaje de error 404
        echo "404 Página no encontrada";
        break;
}