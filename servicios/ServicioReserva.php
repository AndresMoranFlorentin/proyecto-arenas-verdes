<?php
require_once './models/ParcelaModel.php';
require_once './models/authModel.php';
require_once './helpers/ToolsHelper.php';
/**
 * Este archivo se encarga de complementar al controlador de reservas
 * con aquellas funciones que necesita el controller pero que no son
 * tan importantes(las funciones del archivo estan estrechamente 
 * vinculadas con las reservas)
 */
class ServicioReserva
{
    /** @var ReservaModel  */
    private $reservaModel;
    /** @var ParcelaModel*/
    private $parcelaModel;
     /** @var authModel*/
    private $userModel;
    /** @var ToolsHelper */
    private $toolsHelper;

    public function __construct()
    {
        $this->reservaModel = new ReservaModel();
        $this->toolsHelper = new ToolsHelper();
        $this->parcelaModel = new ParcelaModel();
        $this->userModel = new authModel();
        $this->reservaModel = new ReservaModel();
    }
    /**
     * Funcion encargada de buscar todas aquellas parcelas que 
     * cumplan las condiciones pasadas por parametro y que no esten 
     * reservadas
     * @param array $datos es un array que contiene todos los datos necesarios
     * para filtrar aquellas parcelas que cumplan lo pedido
     * @return array|null retorna un array con todas aquellas parcelas que haya encontrado o null
     */
    public function buscarParcelasDisponibles($datos)
    {
        // Lógica de negocio para filtrar parcelas
        return $this->reservaModel->buscarParcelasDisponibles(
            $datos['inicio'],
            $datos['fecha_fin'],
            $datos['personas'],
            $datos['tipo_de_vehiculo'],
            $datos['fogon'] ?? 0,
            $datos['tomaElectrica'] ?? 0,
            $datos['sombra'] ?? 0,
            $datos['agua'] ?? 0
        );
    }
    /**
     * Funcion que cumple la funcion de retornar true en caso de que la fecha
     * inicio sea antes de la fecha fin pasada por parametro
     * @param string $fecha_inicio 
     * @param string $fecha_fin
     * @return true|false si la fecha_inicio<fecha_fin
     */
    public function controlFechasInicioFin($fecha_inicio, $fecha_fin)
    {
        $fecha_inicio_obj = new DateTime($fecha_inicio);
        $fecha_fin_obj = new DateTime($fecha_fin);

        return $fecha_inicio_obj < $fecha_fin_obj;
    }
    /**
     * Funcion encargada de calcular el precio estimativo de lo que seria una reserva
     * de ciertas caracteristicas pasadas por parametro
     * @param int $edadninos4 edad de los niños hasta los 4 años
     * @param int $edadninos12 edad de los niños superiores a 4 años y menores de 12
     * @param int $edadninos20 edad de los niños y adultos superiores a 12 años
     * @param int $tiempo_estancia el numero de dias de estadia de la reservacion
     * @param int $con_ducha (0|1) si incluye ducha o no
     * @param string $tipo_estadia si el grupo va a acampar o pasar el dia
     * @param string $tipo_vehiculo el tipo de vehiculo que usara en la parcela
     * @param boolean $residente si es residente local(de loberia) o no(muy importante para calcular el precio)
     * @return float retorna el precio total calculado de una reservacion de tales caracteristicas
     *  */
    public function calcularPrecio(
        $edadninos4,
        $edadninos12,
        $edadninos20,
        $tiempo_estancia,
        $con_ducha,
        $tipo_estadia,
        $tipo_vehiculo,
        $residente
    ) {
        //se le pide desde la base de datos la tabla que contiene todos los precios de lo que cuesta una reserva
        $tabla_precios = $this->reservaModel->getPrecios($residente);
        //de este modo solo utilizo los precios que se encuentran en la primera columna
        $tabla_precios = $tabla_precios[0];
        //inicializo a la variable deñ precio total de la reservacion
        $precio_final=0;
        //aqui se hace una division dependiendo de si van a acampar o pasar el dia(quiere decir que no se quedaran a la noche)
        if ($tipo_estadia == 'acampar') {
            //costo por en numero de niños y su categoria de edad
            $precio_ninos4 = $edadninos4 * ($tabla_precios['edad_ninos4'] ?? 0);
            $precio_ninos12 = $edadninos12 * ($tabla_precios['edad_ninos12'] ?? 0);
            $precio_ninos20 = $edadninos20 * ($tabla_precios['edad_ninos20'] ?? 0);
            $precio_final = ($precio_ninos4 + $precio_ninos12 + $precio_ninos20)*$tiempo_estancia;
        }
        //en el caso de que sea pasar el dia los costos cambiaran
        else if ($tipo_estadia == 'pasar_dia') {

            $cant_personas = ($edadninos4 + $edadninos12 + $edadninos20);
            $precio_final = ($cant_personas * $tabla_precios['costo_estancia_xdia'])*$tiempo_estancia;
        }
        //siempre se le incluye el precio de sanitario multiplicado por los dias de estancia
        $precio_final += ($tabla_precios['costo_sanitario']*$tiempo_estancia);
        //en caso de que eligio la opcion de ducha
        if ($con_ducha) {
            //al monto se le suma el costo de la ducha por la cantidad de dias que se quedaran
            $precio_final += ($tabla_precios['costo_ducha'] * $tiempo_estancia);
        }
        if ($tipo_vehiculo != null) { //en caso de que eligio llevar un vehiculo
            //en el caso especial de que llevara una casilla se hace un calculo especial, calculando los dias o meses de estadia
            if ($tipo_vehiculo == 'casilla') {
                $meses = floor($tiempo_estancia / 30); //calcula cuantos meses de estancia son
                // Calcular los días restantes que no completan un mes
                $dias = $tiempo_estancia % 30;
                $precio_final += ($meses * $tabla_precios['costoxmescasilla']) + ($dias * $tabla_precios['costoxcasillaxdia']);
            }
            else{
            //en caso de que deje algun vehiculo distinto de una casilla se le cobra un precio fijo por dia de estancia
            $precio_final += ($tiempo_estancia * $tabla_precios['costoxvehiculoxdia']);
       
            }
            } else { //camping seria el equivalente a no llevar vehiculos
            //por el momento si no lleva vehiculo no se le cobra nada aparte
        }
        return $precio_final;
    }
    /**
     * Funcion encargada de validar si todos los datos enviados por el usuario 
     * no son nulls o esten vacios y que la fecha de reservacion no sea incoherente
     * @param array $datos contiene todos aquellos datos que son enviados por el formulario de reservacion
     * @return boolean devuelve true si los datos dados pasaron todos los filtros
     */
    public function validacionDatosReservacion($datos)
    {
        $camposRequeridos = ['nombre', 'apellido', 'dni', 'inicio', 'fecha_fin','tipo_estadia','tipo_de_vehiculo'];
        $fecha_actual = date('Y-m-d');
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                return false;
            }
        }
                   //se busca si existe ese usuario mediante el dni
        $dni = $_POST['dni']; //dni de quien hace la reserva
        $datos_user = $this->userModel->findUserByDni($dni);
        
        if($datos_user==null){
            return false;
        }
        if (!($this->controlFechasInicioFin($datos['inicio'], $datos['fecha_fin']))) {
            return false;
        }
        //si la fecha de la reservacion de inicio es menor a la actual es una incoherencia
        //no se puede reservar antes de la fecha actual, ni tampoco la fecha inicio puede ser exactamente igual
        //a la de fin solo seria un dia de reservacion la condicion del if obliga que al menos sean dos dias
        if (($fecha_actual > $datos['inicio']) & ($datos['inicio'] == $datos['fecha_fin'])) {
            return false;
        }
        //si todas las verificaciones fueron valida entonces retorna true
        return true;
    }
    /**
     * Funcion que retorna los dias de diferencia entre dos fechas de inicio y fin
     * @param string $fecha_inicio la fecha de inicio del intervalo
     * @param string @fecha fin la fecha de fin del intervalo
     * @return int $dias el numero de dias entre las dos fechas pasadas 
     */
    public function retornarDiasDeDiferencia($fecha_inicio, $fecha_fin)
    {
        $fecha_inicio_obj = new DateTime($fecha_inicio);
        $fecha_fin_obj = new DateTime($fecha_fin);
        $dias_de_estancia = $fecha_inicio_obj->diff($fecha_fin_obj);
        $dias = $dias_de_estancia->days;
        return $dias;
    }
    /**
     * Funcion encargada de clasificar(numericamente) por sector de parcela
     * el conjunto de parcelas que recibe por parametro 
     * 
     * @param array $parcelas es un array de las parcelas dadas
     * @return array $asociativo es un array asociativo que devuelve una 
     * palabra clave(el sector) junto a el numero de parcelas que le corresponden
     */
    public function agruparPorSector($parcelas)
    {
        $asociativo = [
            "Familiar" => 0,
            "Carpa Fam" => 0,
            "Joven" => 0,
            "Motorhome" => 0
        ];

        foreach ($parcelas as $par) {
            if ($par['sector'] == "Familiar") {
                $asociativo["Familiar"] += 1;
            } elseif ($par['sector'] == "Carpa Fam") {
                $asociativo["Carpa Fam"] += 1;
            } elseif ($par['sector'] == "Joven") {
                $asociativo["Joven"] += 1;
            } elseif ($par['sector'] == "Motorhome") {
                $asociativo["Motorhome"] += 1;
            }
        }

        return $asociativo;
    }
    /**
     * Esta funcion es la encargada de no solo inhabilitar la parcela si no de eliminar
     * aquellas reservas que se hayan hecho sobre ella en la fecha actual que se pida inhabilitar
     */
    public function inhabilitarParcelaYReservacionRelacionada($id_parcela)
    {
        // 1) Buscar al usuario que reservó la parcela
        $usuario = $this->parcelaModel->estaReservadaParcela($id_parcela);
        if (!empty($usuario)) {
            $id_usuario = $usuario[0]['id'];

            // 2) Buscar la reservación relacionada
            $reserva = $this->reservaModel->getParcelaReservada($id_parcela, $id_usuario);
            if (!empty($reserva)) {
                $id_reserva = $reserva[0]['id'];

                // 3) Eliminar la reservación
                $this->reservaModel->eliminarRelacionParcelaReserva($id_reserva);
                $this->reservaModel->eliminarReserva($id_reserva);
            }
        }

        // 4) Finalmente, inhabilitar la parcela
        $this->parcelaModel->inhabilitarParcela($id_parcela);

        return $usuario;
    }
}
