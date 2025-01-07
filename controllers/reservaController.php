
<?php
require_once './views/reservaView.php';
require_once './helpers/sessionHelper.php';
require_once './models/ReservaModel.php';


class ReservaController
{
    private $view;
    private $helper;
    private $model;

    function __construct()
    {
        $this->model = new ReservaModel();
        $this->view = new ReservaView();
        $this->helper = new SessionHelper();
    }

    public function showHome()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->showHome($logueado, $rol);
    }

    public function renderPrecios()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->renderPrecios($logueado, $rol);
    }
    public function buscarParcelasDispo()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        if (
            !isset($_POST['inicio'])
            && !isset($_POST['fecha_fin'])
            && !isset($_POST['personas'])
            && !isset($_POST['tipo_de_vehiculo'])
        ) {
            //vuelve a enviarte a la pagina de reservacion8faltaria mejorar para que muestre un mensaje
            //de error por envio de datos incompletos 
            $this->view->reservacion();
        }
        $inicio = $_POST['inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $personas = $_POST['personas'];
        $tipo_de_vehiculo = $_POST['tipo_de_vehiculo'];
        // Captura las características de la parcela 
        $caracteristicas = [
            'fogon' => in_array(
                'fogon',
                $_POST['caracteristicas'] ?? []
            ),
            'tomaElectrica' => in_array(
                'tomaElectrica',
                $_POST['caracteristicas'] ?? []
            ),
            'sombra' => in_array(
                'sombra',
                $_POST['caracteristicas'] ?? []
            ),
            'agua' => in_array(
                'agua',
                $_POST['caracteristicas'] ?? []
            )
        ];
        $con_fogon = ($caracteristicas['fogon'] ? 1 : 0);
        $con_electricidad = ($caracteristicas['tomaElectrica'] ? 1 : 0);
        $con_sombra = ($caracteristicas['sombra'] ? 1 : 0);
        $con_agua = ($caracteristicas['agua'] ? 1 : 0);
        //
        //faltarian agregarles algunos controles como que si o si debe haber al menos una persona y jamas un numero negativo
        //cuando se eligen las fechas, jamas la fecha de inicio debe ser mas adelantada que la fecha de fin
        $reservaciones = $this->model->buscarParcelasDisponibles(
            $inicio,
            $fecha_fin,
            $personas,
            $tipo_de_vehiculo,
            $con_fogon,
            $con_electricidad,
            $con_sombra,
            $con_agua
        );
        //funciona, me trae las parcelas que se ajustan a las condiciones dadas por el usuario
        //falta mostrarlas de forma elegante con una vista bien hecha
        $this->view->mostrarParcelasDisponibles($reservaciones);
    }
    //funcion encargada de recibir las condiciones de la reserva y simular los precios
    //de una posible reserva
    public function simularPrecioReserva()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        if (
            !isset($_POST['edad_ninos4'])
            && !isset($_POST['edad_ninos12'])
            && !isset($_POST['edad_ninos20'])
            && !isset($_POST['estancia'])
        ) {
            //vuelve a enviarte a la pagina de precio faltaria mejorar para que muestre un mensaje
            //de error por envio de datos incompletos 
            $this->view->renderPrecios($logueado, $rol);
        }
        $edadninos4 = $_POST['edad_ninos4']; //atributo de aquellas personas de hasta 4 años
        $edadninos12 = $_POST['edad_ninos12']; //atributo de aquellas personas entre 4 y 12 años
        $edadninos20 = $_POST['edad_ninos20']; //atributo de aquellas personas mayores de 12 años
        $tiempo_estancia = $_POST['estancia']; //el tiempo de estancia que calcula estar
        // Captura las características de la parcela 
        $caracteristicas = [
            'ducha' => in_array(
                'ducha',
                $_POST['caracteristicas'] ?? []
            ),
            'sanitario' => in_array(
                'sanitario',
                $_POST['caracteristicas'] ?? []
            ),
            'casilla' => in_array(
                'casilla',
                $_POST['caracteristicas'] ?? []
            ),
            'vehiculo' => in_array(
                'vehiculo',
                $_POST['caracteristicas'] ?? []
            )
        ];
        $con_ducha = ($caracteristicas['ducha'] ? 1 : 0);
        $con_sanitario = ($caracteristicas['sanitario'] ? 1 : 0);
        $llevar_casilla = ($caracteristicas['casilla'] ? 1 : 0);
        $llevar_vehiculo = ($caracteristicas['vehiculo'] ? 1 : 0);
        $residente_loberia = $_POST['residentes'];
        //se le pide desde la base de datos la tabla que contiene
        //todos los precios de lo que cuesta una reserva
        $tabla_precios = $this->model->getPrecios($residente_loberia);

        //de este modo solo utilizo los precios que se encuentran en la primera columna
        //ya que nunca se agregaran nuevos precios, en todo caso se actualizaran o dejaran vacios
        $tabla_precios = $tabla_precios[0];
        //cargo a la variable el precio total de lo que costo
        $precio_final = $this->calcularPrecio(
            $edadninos4,
            $edadninos12,
            $edadninos20,
            $tiempo_estancia,
            $con_ducha,
            $con_sanitario,
            $llevar_casilla,
            $llevar_vehiculo,
            $tabla_precios
        );
        $this->view->mostrarPrecioParcela($precio_final);
    }
    private function calcularPrecio(
        $edadninos4,
        $edadninos12,
        $edadninos20,
        $tiempo_estancia,
        $con_ducha,
        $con_sanitario,
        $llevar_casilla,
        $llevar_vehiculo,
        $tabla_precios
    ) {
        //costo por en numero de niños y su categoria de edad
        $precio_ninos4 = $edadninos4 * ($tabla_precios['edad_ninos4'] ?? 0);
        $precio_ninos12 = $edadninos12 * ($tabla_precios['edad_ninos12'] ?? 0);
        $precio_ninos20 = $edadninos20 * ($tabla_precios['edad_ninos20'] ?? 0);
        $precio_final = $precio_ninos4 + $precio_ninos12 + $precio_ninos20;

        if ($con_ducha) { //en caso de que eligio la reserva con ducha
            //al monto se le suma el costo de la ducha
            $precio_final += $tabla_precios['costo_ducha'];
        }
        if ($con_sanitario) { //en caso de que eligio la reserva con sanitario
            $precio_final += $tabla_precios['costo_sanitario'];
        }
        if ($llevar_casilla) { //en caso de que eligio llevar una casilla
            $meses = floor($tiempo_estancia / 30); //calcula cuantos meses de estancia son
            // Calcular los días restantes que no completan un mes
            $dias = $tiempo_estancia % 30;
            $precio_final += ($meses * $tabla_precios['costoxmescasilla']) + ($dias * $tabla_precios['costoxcasillaxdia']);
        }
        if ($llevar_vehiculo) { //en caso de que eligio llevar un vehiculo
            $precio_final += $tabla_precios['costoxvehiculoxdia'] * $tiempo_estancia;
        }
        //se le agrega al monto el costo de la estadia por dia
        $precio_final += ($tabla_precios['costo_estancia_xdia'] * $tiempo_estancia);

        return $precio_final;
    }
    public function preguntasFrec()
    {
        $this->view->pregFrec();
    }
    public function reservacion()
    {
        $this->view->reservacion();
    }
        //Muestra los distintos sectores
        public function sectoresParcelas()
    {
        $this->view->parcelas();
    }
}
