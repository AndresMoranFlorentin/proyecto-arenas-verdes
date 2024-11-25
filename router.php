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
    case 'home':
        $reservaController->showHome();
        break;
    case 'login':
        $authController->showLogin();
        break;
    case 'auth':
        $authController->auth();
        break;
    case 'register':
        $authController->register();
        break;
    default:
        echo "404 Página no encontrada";
        break;
}
