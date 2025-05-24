<?php
require_once './models/InformeModel.php';
require_once './helpers/sessionHelper.php';

class InformeController
{
    private $model;
    private $helper;

    public function __construct()
    {
        $this->model = new InformeModel();
        $this->helper = new SessionHelper();
    }

    public function mostrarVistaInformes()
    {
        $informes = $this->model->obtenerInformes();
        include __DIR__ . '/../templates/generar_informes.phtml';
    }

    public function generarInforme()
    {
        if (!isset($_GET['fecha']) || !isset($_GET['tipo_informe'])) {
            echo "Faltan parámetros.";
            return;
        }

        $fecha = $_GET['fecha'];
        $tipo = $_GET['tipo_informe'];
        $informe = $this->obtenerResultado($tipo, $fecha);

        echo $informe;
    }

    public function obtenerResultado($tipo, $fecha)
    {
        switch ($tipo) {
            case 'reservas':
                return $this->model->obtenerReservasPorFecha($fecha);
            case 'acampantes':
                return $this->model->obtenerCantidadAcampantes($fecha);
            case 'ocupacion':
                return $this->model->obtenerNivelOcupacion($fecha);
            case 'ingresos':
                return $this->model->obtenerIngresosSemanales($fecha);
            default:
                return "Tipo de informe inválido.";
        }
    }
}