<?php
// router.php
require_once './controllers/reservaController.php';
require_once './controllers/AuthController.php';


define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

$action = 'home'; // Acción por defecto
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

$params = explode('/', $action);

switch ($params[0]) {
    case 'home':
        $controller = new ReservaController();
        $controller->showHome();
        break;
    case 'login':
        $controller = new AuthController();
        $controller->showLogin();
        break;
    case 'agregarUsuario':
        $controller = new AuthController();
        $controller->agregarUsuario();
        break;
    case 'preg':
        $controller = new ReservaController();
        $controller->preguntasFrec();
        break;
    case 'reservacion':
        $controller = new ReservaController();
        $controller->reservacion();
        break;
    case 'precios':
        $controller = new ReservaController();
        $controller->precios();
        break;
    default:
        echo "404 Página no encontrada";
        break;
}
