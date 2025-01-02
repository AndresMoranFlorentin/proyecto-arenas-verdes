<?php
class ServicioReserva
{
    private $reservaModel;
    private $toolsHelper;

    public function __construct()
    {
        $this->reservaModel = new ReservaModel();
        $this->toolsHelper = new ToolsHelper();
    }
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
    public function controlFechasInicioFin($fecha_inicio, $fecha_fin)
    {
        $fecha_inicio_obj = new DateTime($fecha_inicio);
        $fecha_fin_obj = new DateTime($fecha_fin);

        return $fecha_inicio_obj < $fecha_fin_obj;
    }
    //funcion encargada de calcular el precio estimativo de lo que seria una reserva
    //de x caracteristicas
    public function calcularPrecio(
        $edadninos4,
        $edadninos12,
        $edadninos20,
        $tiempo_estancia,
        $con_ducha,
        $con_sanitario,
        $tipo_vehiculo,
        $residente
    ) {
         //se le pide desde la base de datos la tabla que contiene todos los precios de lo que cuesta una reserva
         $tabla_precios = $this->reservaModel->getPrecios($residente);
         //de este modo solo utilizo los precios que se encuentran en la primera columna
         $tabla_precios = $tabla_precios[0];
         //cargo a la variable el precio total de lo que costo
         
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
        if ($tipo_vehiculo=='casilla') { //en caso de que eligio llevar una casilla
            $meses = floor($tiempo_estancia / 30); //calcula cuantos meses de estancia son
            // Calcular los días restantes que no completan un mes
            $dias = $tiempo_estancia % 30;
            $precio_final += ($meses * $tabla_precios['costoxmescasilla']) + ($dias * $tabla_precios['costoxcasillaxdia']);
        }
        else if($tipo_vehiculo=='camping'){//camping seria el equivalente a no llevar vehiculos

        }
        else { //en caso de que eligio llevar un vehiculo
            $precio_final += $tabla_precios['costoxvehiculoxdia'] * $tiempo_estancia;
        }
        //se le agrega al monto el costo de la estadia por dia
        $precio_final += ($tabla_precios['costo_estancia_xdia'] * $tiempo_estancia);

        return $precio_final;
    }
    public function validacionDatosReservacion($datos)
    {
        $camposRequeridos = ['nombre', 'apellido', 'dni', 'inicio', 'fecha_fin', 'tipo_de_vehiculo'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                return false;
            }
        }
        if (!($this->controlFechasInicioFin($datos['inicio'], $datos['fecha_fin']))) {
            return false;
        }
        //si todas las verificaciones fueron valida entonces retorna true
        return true;
    }
    public function retornarDiasDeDiferencia($fecha_inicio, $fecha_fin)
    {
        $fecha_inicio_obj = new DateTime($fecha_inicio);
        $fecha_fin_obj = new DateTime($fecha_fin);
        $dias_de_estancia = $fecha_inicio_obj->diff($fecha_fin_obj);
        return $dias_de_estancia->days;
    }
}
