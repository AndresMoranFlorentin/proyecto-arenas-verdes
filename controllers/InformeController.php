<?php
require_once './models/InformeModel.php';
require_once './helpers/sessionHelper.php';
class InformeController {
    private $model;
    private $helper;

    public function __construct() {
        $this->model = new InformeModel();
        $this->helper = new SessionHelper();
    }

    public function mostrarVistaInformes() {
        // Obtener datos si es necesario
        $informes = $this->model->obtenerInformes();
        
        // Incluir la vista
        include './views/generar_informes.phtml';
    }

    public function generarInforme() {
        if (!isset($_GET['fecha']) || !isset($_GET['tipo_informe'])) {
            return;
        }

        $fecha = $_GET['fecha'];
        $tipo = $_GET['tipo_informe'];
        $informe = "";

        switch ($tipo) {
            case 'reservas':
                $informe = $this->model->obtenerReservasPorFecha($fecha);
                break;
            case 'acampantes':
                $informe = $this->model->obtenerCantidadAcampantes($fecha);
                break;
            case 'ocupacion':
                $informe = $this->model->obtenerNivelOcupacion($fecha);
                break;
            case 'ingresos':
                $informe = $this->model->obtenerIngresosSemanales($fecha);
                break;
        }
        
        include 'views/generar_informes.phtml';
    }
}
