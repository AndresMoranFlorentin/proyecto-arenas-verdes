<?php
// router.php
require_once './controllers/reservaController.php';
require_once './controllers/authController.php';
require_once './controllers/ParcelaController.php';

define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

$action = 'home'; // Acción por defecto
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

$reservaController = new ReservaController();
$authController = new AuthController();

$params = explode('/', $action);
switch ($params[0]) {
//------FUNCIONES DEL CONTROLLER RESERVA
    case 'home':
        $reservaController->showHome();
        break;
    case 'precios':
        $reservaController->renderPrecios();
        break;
    case 'simular_precios':
        $controller = new ReservaController();
        $controller->simularPrecioReserva();  
        break;
    case 'buscarParcelasDispo':
        $controller = new ReservaController();
        $controller->buscarParcelasDispo();  
        break;   
    case 'pedir_reservacion':
        $controller = new ReservaController();
        $controller->pedirReservacion();
        break;
    case 'generar_reservacion':
        $controller = new ReservaController();
        $controller->generarReservacion();
        break;
        break;
    case 'parcelas':
        $controller = new ReservaController();
        $controller->sectoresParcelas();
        break;       
    case 'preguntas':
        $controller = new ReservaController();
        $controller->preguntasFrec();
        break;
    case 'reservacion':
        $controller = new ReservaController();
        $controller->reservacion();
        break;
    case 'crt_parcelas':
        $controller= new ParcelaController();
        $controller->seccionParcelas(); 
        break;
    case 'habilitar':
        $controller= new ParcelaController();
        $controller->habilitarParcela(); 
        break;
    case 'inhabilitar':
        $controller= new ParcelaController();
        $controller->inhabilitarParcela(); 
        break;
    //-----FIN DE LAS FUNCIONES DEL RESERVA CONTROLLER---  
     //*******RESERVA CONTROLLER**********************
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
