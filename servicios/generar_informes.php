<?php
require_once './controllers/InformeController.php';

$controller = new InformeController();

if (isset($_GET['fecha']) && isset($_GET['tipo_informe'])) {
    $_GET['ajax'] = true;
    $controller->generarInforme();
} else {
    echo "Faltan parámetros.";
}

