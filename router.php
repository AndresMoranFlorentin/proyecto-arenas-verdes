<?php
// router.php
require_once './controllers/reservaController.php';
require_once './controllers/authController.php';


define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

$action = 'home'; // Acción por defecto
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

$reservaController = new ReservaController();
$authController = new AuthController();

$params = explode('/', $action);

switch ($params[0]) {
        //*******RESERVA CONTROLLER**********************
    case 'home':
        $reservaController->showHome();
        break;
    case 'precios':
        $reservaController->renderPrecios();
        break;
        //*****AUTH CONTROLLER **************  
    case 'preguntas':
        $controller = new ReservaController();
        $controller->preguntasFrec();
        break;
    case 'reservacion':
        $controller = new ReservaController();
        $controller->reservacion();    
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

    default:
        echo "404 Página no encontrada";
        break;
}
