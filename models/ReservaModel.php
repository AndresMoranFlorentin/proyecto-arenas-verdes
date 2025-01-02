<?php
require_once 'conectionModel.php';

class ReservaModel extends ConectionModel
{
    protected $conexion;

    function __construct()
    {
        parent::__construct();
        $this->conexion = $this->getConection();
    }

    public function buscarParcelasDisponibles($inicio, $fecha_fin, $personas, $tipo_de_vehiculo, $con_fogon, $con_toma_electrica, $sombra, $agua)
    {


        $sql = "SELECT p.* 
                FROM parcela p
                LEFT JOIN servicioreserva s ON p.id_servicio = s.id_servicio
                LEFT JOIN reserva_parcela rp ON p.id = rp.id_parcela
                LEFT JOIN reserva r ON rp.id_reserva = r.id
                WHERE (
                    (
                        r.fecha_inicio NOT BETWEEN :inicio AND :fecha_fin 
                        AND r.fecha_fin NOT BETWEEN :inicio AND :fecha_fin
                        AND :inicio NOT BETWEEN r.fecha_inicio AND r.fecha_fin
                        AND :fecha_fin NOT BETWEEN r.fecha_inicio AND r.fecha_fin
                    ) 
                    OR r.id IS NULL
                )";

        // Agregar filtros dinámicos para las características
        if ($con_fogon !== null) {
            $sql .= " AND s.con_fogon = :con_fogon";
        }
        if ($con_toma_electrica !== null) {
            $sql .= " AND s.con_toma_electrica = :con_toma_electrica";
        }
        if ($sombra !== null) {
            $sql .= " AND s.sombra = :sombra";
        }
        if ($agua !== null) {
            $sql .= " AND s.agua = :agua";
        }

        // Preparar la consulta
        $resultado = $this->conexion->prepare($sql);

        // Asignar los parámetros
        $params = [
            ':inicio' => $inicio,
            ':fecha_fin' => $fecha_fin,
        ];
        if ($con_fogon !== null) {
            $params[':con_fogon'] = $con_fogon;
        }
        if ($con_toma_electrica !== null) {
            $params[':con_toma_electrica'] = $con_toma_electrica;
        }
        if ($sombra !== null) {
            $params[':sombra'] = $sombra;
        }
        if ($agua !== null) {
            $params[':agua'] = $agua;
        }

        // Ejecutar la consulta
        $resultado->execute($params);

        // Obtener los resultados
        $parcelas = $resultado->fetchAll(PDO::FETCH_ASSOC);

        return $parcelas;
    }
    public function getParcelaDisponible($fecha_inicio, $fecha_fin, $cantPersonas, $tipo_de_vehiculo, $fogon, $tomaElectrica, $sombra, $agua) {
        $sql = "
        SELECT p.* 
        FROM parcela AS p
        INNER JOIN servicioreserva AS sr ON p.id_servicio = sr.id_servicio
        WHERE 
            (sr.con_fogon = ? AND sr.con_toma_electrica = ? AND sr.sombra = ? AND sr.agua = ?)
            AND p.cant_personas >= ?
            AND p.id NOT IN (
                SELECT DISTINCT reserpar.id_parcela
                FROM reserva_parcela AS reserpar
                INNER JOIN reserva AS r ON reserpar.id_reserva = r.id
                WHERE NOT (r.fecha_fin < ? OR r.fecha_inicio > ?)
            )
        LIMIT 1";

    // Ejecutar la consulta
    $resultado = $this->conexion->prepare($sql);
    $resultado->execute([$fogon, $tomaElectrica, $sombra, $agua, $cantPersonas, $fecha_inicio, $fecha_fin]);
    $parcela = $resultado->fetch(PDO::FETCH_ASSOC);

    return $parcela;
    }
    public function nuevaReserva($id_user, $fecha_inicio, $fecha_fin, $tipo_de_vehiculo, $id_servicio, $estado, $identificador) {
        $sql = 'INSERT INTO reserva (id_usuario, fecha_inicio, fecha_fin,tipo_vehiculo, id_servicio, estado, identificador) 
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([$id_user, $fecha_inicio, $fecha_fin, $tipo_de_vehiculo, $id_servicio, $estado, $identificador]);
        // Obtener el ID de la nueva reserva
        $nuevo_id = $this->conexion->lastInsertId();
        return $nuevo_id;
    }
    
    //funcion donde traera todos los precios guardados en la bbdd
    public function getPrecios($residente)
    {

        $sql = "SELECT p.* FROM precios AS p WHERE p.residente_local = ? ";
        // Ejecutar la consulta
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$residente]);
        $precios = $resultado->fetchAll(PDO::FETCH_ASSOC);

        return $precios;
    }
    public function findServicio($fogon,$tomaElectrica,$sombra,$agua){
        $sql="SELECT s.id_servicio FROM servicioreserva AS s
              WHERE s.con_fogon=? 
              AND s.sombra= ?
              AND s.con_toma_electrica=? 
              AND s.agua= ?";
        $servicio = $this->conexion->prepare($sql);
        $servicio->execute([$fogon,$sombra,$tomaElectrica,$agua]);
        $resultado = $servicio->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }
    //funcion que agrega un nuevo servicio adicional
    public function insertServicioAdicional($fogon,$tomaElectrica,$sombra,$agua){
        $sql = 'INSERT INTO servicioreserva (con_fogon,con_toma_electrica,sombra,agua) VALUES (?, ?, ?, ?)';
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([$fogon,$sombra,$tomaElectrica,$agua]);
    }
    public function crearRelacionParcela($id_nueva_reserva,$id_parcela){
        $sql = "INSERT INTO reserva_parcela (id_reserva, id_parcela) VALUES (?, ?)";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([$id_nueva_reserva,$id_parcela]);
    }
}
