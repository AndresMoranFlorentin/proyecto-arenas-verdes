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
    if (!isset($_GET['tipo_informe'])) {
        echo "Falta el tipo de informe.";
        return;
    }

    $tipo = $_GET['tipo_informe'];

    if (isset($_GET['fecha'])) {
        $fecha = $_GET['fecha'];
        $informe = $this->obtenerResultado($tipo, $fecha);
        echo $informe;
        return;
    }

    if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
        $inicio = $_GET['fecha_inicio'];
        $fin = $_GET['fecha_fin'];
        $informe = $this->obtenerResultado($tipo, $inicio, $fin); // ajustá tu método si recibe rango
        echo $informe;
        return;
    }

    echo "Faltan parámetros. No se recibió ni 'fecha' ni 'fecha_inicio/fecha_fin'.";
}
    public function obtenerResultado($tipo, $fecha,$fecha_fin=null)
    {
        switch ($tipo) {
            case 'reservas':
                return $this->model->obtenerReservasPorFecha($fecha,$fecha_fin);
            case 'acampantes':
                return $this->model->obtenerCantidadAcampantes($fecha,$fecha_fin);
            case 'ocupacion':
                return $this->model->obtenerNivelOcupacion($fecha,$fecha_fin);
            case 'ingresos':
                return $this->model->obtenerIngresosSemanales($fecha,$fecha_fin);
            default:
                return "Tipo de informe inválido.";
        }
    }
}