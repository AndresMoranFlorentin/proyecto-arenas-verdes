<?php
// router.php
require_once './controllers/reservaController.php';


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
    default:
        echo "404 Página no encontrada";
        break;
}
?>