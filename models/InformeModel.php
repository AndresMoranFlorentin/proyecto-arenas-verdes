<?php
require_once 'conectionModel.php';
class InformeModel extends ConectionModel {
    protected $conexion;

    function __construct()
    {
        parent::__construct();
        $this->conexion = $this->getConection();
    }

    public function obtenerInformes() {
        // Aquí puedes hacer consultas a la base de datos para los informes
        return []; // Por ahora retorna un array vacío
    }

    public function obtenerReservasPorFecha($fecha) {
        $query = "SELECT COUNT(*) as total FROM reserva WHERE fecha_inicio <= ? AND fecha_fn >= ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$fecha, $fecha]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] . " reservas encontradas.";
    }

    public function obtenerCantidadAcampantes($fecha) {
        $query = "SELECT SUM(menores_de_4 + menores_de_12 + mayores_de_12) as total FROM reserva WHERE fecha_inicio <= ? AND fecha_fn >= ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$fecha, $fecha]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] . " personas acampando.";
    }

    public function obtenerNivelOcupacion($fecha) {
        $query = "SELECT COUNT(*) as ocupadas FROM reserva_parcela JOIN reserva ON reserva_parcela.id_reserva = reserva.id WHERE fecha_inicio <= ? AND fecha_fn >= ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$fecha, $fecha]);
        
        $totalParcelasQuery = "SELECT COUNT(*) as total FROM parcela";
        $stmtTotal = $this->conexion->query($totalParcelasQuery);
        
        $ocupadas = $stmt->fetch(PDO::FETCH_ASSOC)['ocupadas'];
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
        
        return "Nivel de ocupación: " . round(($ocupadas / $total) * 100, 2) . "%";
    }
    public function obtenerIngresosSemanales($fecha) {
        $query="SELECT SUM(r.precio_total) AS ganancia_semana
                FROM reserva AS r
                WHERE  r.fecha_inicio <= DATE_ADD(?, INTERVAL (6 - WEEKDAY(?)) DAY)
                AND r.fecha_fin >= DATE_SUB(?, INTERVAL WEEKDAY(?) DAY);
                ";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$fecha, $fecha,$fecha, $fecha]);
        return "$" . number_format($stmt->fetch(PDO::FETCH_ASSOC)['ganancia_semana'], 2) . " en ingresos generados.";
   
    }
   /* public function obtenerIngresosSemanales($fecha) {
        $query = "SELECT SUM(precios.costo_estancia_xdia * DATEDIFF(fecha_fn, fecha_inicio)) as total FROM reserva JOIN precios ON reserva.id_usuario = precios.residente_local WHERE fecha_inicio >= DATE_SUB(?, INTERVAL 7 DAY) AND fecha_fn <= ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$fecha, $fecha]);
        return "$" . number_format($stmt->fetch(PDO::FETCH_ASSOC)['total'], 2) . " en ingresos generados.";
    }*/
}
