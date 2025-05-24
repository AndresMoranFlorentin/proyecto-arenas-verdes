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
        return [];
    }

    public function obtenerReservasPorFecha($fecha) {
        $query = "SELECT COUNT(*) as total FROM reserva WHERE fecha_inicio <= ? AND fecha_fin >= ? AND estado = 'confirmada'";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$fecha, $fecha]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        return $total > 0 ? "$total reservas encontradas." : "No se encontraron reservas para esa fecha.";
    }

    public function obtenerCantidadAcampantes($fecha) {
        $query = "SELECT 
                    SUM(menores_de_4) AS menores_4, 
                    SUM(menores_de_12) AS menores_12, 
                    SUM(mayores_de_12) AS mayores 
                  FROM reserva
                  WHERE fecha_inicio <= ? AND fecha_fin >= ? AND estado = 'confirmada'";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$fecha, $fecha]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $data['menores_4'] + $data['menores_12'] + $data['mayores'];
        if ($total > 0) {
            return "Total: $total personas<br>Menores de 4: {$data['menores_4']}<br>Menores de 12: {$data['menores_12']}<br>Mayores de 12: {$data['mayores']}";
        } else {
            return "No hay personas acampando en la fecha seleccionada.";
        }
    }

    public function obtenerNivelOcupacion($fecha) {
        $queryOcupadas = "SELECT COUNT(DISTINCT rp.id_parcela) as ocupadas
                          FROM reserva_parcela rp
                          JOIN reserva r ON rp.id_reserva = r.id
                          WHERE r.fecha_inicio <= ? AND r.fecha_fin >= ? AND r.estado = 'confirmada'";
        $stmt = $this->conexion->prepare($queryOcupadas);
        $stmt->execute([$fecha, $fecha]);
        $ocupadas = $stmt->fetch(PDO::FETCH_ASSOC)['ocupadas'];

        $queryTotal = "SELECT COUNT(*) as total FROM parcela WHERE disponible = 'disponible'";
        $stmtTotal = $this->conexion->query($queryTotal);
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        if ($total == 0) return "No hay parcelas disponibles registradas.";

        $ocupacion = round(($ocupadas / $total) * 100, 2);
        $libres = $total - $ocupadas;

        return "Ocupadas: $ocupadas<br>Libres: $libres<br>Ocupación: $ocupacion%";
    }

        public function obtenerIngresosSemanales($fecha) {
        $query="SELECT SUM(r.precio_total) AS ganancia_semana
                FROM reserva AS r
                WHERE  r.fecha_inicio <= DATE_ADD(?, INTERVAL (6 - WEEKDAY(?)) DAY)
                AND r.fecha_fin >= DATE_SUB(?, INTERVAL WEEKDAY(?) DAY);
                ";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$fecha, $fecha,$fecha, $fecha]);
        return "*aclaracion: el monto considera las reservaciones sin confirmacion* \n $" . number_format($stmt->fetch(PDO::FETCH_ASSOC)['ganancia_semana'], 2) . " en ingresos generados.";   
    }
 
}