<?php
require_once './controllers/InformeController.php';

$controller = new InformeController();

if (isset($_GET['fecha']) && isset($_GET['tipo_informe'])) {
    // Capturar los parámetros
    $_GET['ajax'] = true; // Para indicar que la respuesta es AJAX
    ob_start(); // Capturar la salida del include
    $controller->generarInforme();
    $response = ob_get_clean(); // Obtener la salida como string
    echo $response;
} else {
    echo "Faltan parámetros.";
}
?>
