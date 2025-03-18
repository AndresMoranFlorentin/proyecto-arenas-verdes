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
$params = explode('/', $action);
// Enrutamiento de las acciones
switch ($params[0]) {

    /**
     * --- Funciones del ReservaController ---
     */
    case 'home':
        // Muestra la página principal
        $reservaController->showHome();
        break;

    case 'precios':
        // Muestra la página de precios
        $reservaController->renderPrecios();
        break;

    case 'simular_precios':
        // Simula el precio de una reserva
        $controller = new ReservaController();
        $controller->simularPrecioReserva();
        break;

    case 'buscarParcelasDispo':
        // Busca las parcelas disponibles para reservar
        $controller = new ReservaController();
        $controller->buscarParcelasDispo();
        break;

    case 'pedir_reservacion':
        // Solicita una reservación
        $controller = new ReservaController();
        $controller->pedirReservacion();
        break;

    case 'generar_reservacion':
        // Genera una nueva reservación
        $controller = new ReservaController();
        $controller->generarReservacion();
        break;

    case 'parcelas':
        // Muestra las parcelas disponibles
        $controller = new ParcelaController();
        $controller->sectoresParcelas();
        break;

    case 'preguntas':
        // Muestra las preguntas frecuentes
        $controller = new ReservaController();
        $controller->preguntasFrec();
        break;

    case 'reservacion':
        // Muestra los detalles de una reservación
        $controller = new ReservaController();
        $controller->reservacion();
        break;

    case 'crt_parcelas':
        // Muestra la sección de parcelas
        $controller = new ParcelaController();
        $controller->seccionParcelas();
        break;

    case 'habilitar':
        // Habilita una parcela
        $controller = new ParcelaController();
        $controller->habilitarParcela();
        break;

    case 'inhabilitar':
        // Inhabilita una parcela
        $controller = new ParcelaController();
        $controller->inhabilitarParcela();
        break;

    case 'mostrar_reservas':
        //Muestra todas las reservas de un usuario
        $controller = new ParcelaController();
        $controller -> mostrarReservas();
        break;

    case 'cancelar_reserva':
        // Cancela una reserva
        $controller = new ReservaController();
        $controller->cancelarReserva();
        break;

    /**
     * --- Funciones del AuthController ---
     */
    case 'login':
        // Muestra el formulario de inicio de sesión
        $authController->showLogin();
        break;

    case 'auth':
        // Procesa la autenticación del usuario
        $authController->auth();
        break;

    case 'newUser':
        // Muestra el formulario de registro de usuario
        $authController->showRegisForm();
        break;

    case 'register':
        // Registra un nuevo usuario
        $authController->register();
        break;

    case 'logout':
        // Cierra la sesión del usuario
        $authController->logout();
        break;

    case 'users':
        // Muestra la lista de usuarios
        $authController->getUsers();
        break;

    case 'editRol':
        // Edita el rol de un usuario (requiere un parámetro adicional)
        $authController->editRol($params[1]);
        break;

    case 'deleteUser':
        // Elimina un usuario (requiere un parámetro adicional)
        $authController->deleteUser($params[1]);
        break;

    /**
     * --- Acción por defecto si no se encuentra la ruta ---
     */
    default:
        // Muestra un mensaje de error 404
        echo "404 Página no encontrada";
        break;
}