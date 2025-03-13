<?php
// router.php
require_once './controllers/reservaController.php';
require_once './controllers/authController.php';
require_once './controllers/resetPassController.php';


define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

$action = 'home'; // Acción por defecto
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

$reservaController = new ReservaController();
$authController = new AuthController();
$passController = new PassResetController();

$params = explode('/', $action);

switch ($params[0]) {
        //*******RESERVA CONTROLLER**********************
    case 'home':
        $reservaController->showHome();
        break;
    case 'precios':
        $reservaController->renderPrecios();
        break;
    case 'preguntas':
        $reservaController->preguntasFrec();
        break;
    case 'reservacion':
        $reservaController->reservacion();
        break;
        //*****AUTH CONTROLLER **************  
    case 'login':
        $authController->showLogin();
        break;
    case 'auth':
        $authController->auth();
        break;
    case 'newUser':
        $authController->showRegisForm();
        break;
    case 'register':
        $authController->register();
        break;
        
    case 'logout':
        $authController->logout();
        break;
    case 'users':
        $authController->getUsers();
        break;
    case 'editRol':
        $authController->editRol($params[1]);
        break;
    case 'deleteUser':
        $authController->deleteUser($params[1]);
        break;
    case 'perfil':
        $authController->verPerfil();
        break;
    case 'editarUser':
        $authController->editarUser();
        break;
    case 'resetPassword':
        $authController->resetPassword();
        break;
    case 'olvideContraseña':
        $passController->sendRecoveryEmail();
        break;
    case 'newPassword':
        $passController->showResetForm($params[1]);
        break;

    default:
        echo "404 Página no encontrada";
        break;
}
