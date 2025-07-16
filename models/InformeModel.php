<?php
require_once 'conectionModel.php';

/**
 * Clase InformeModel
 * -------------------
 * Modelo que se conecta a la base de datos y permite generar distintos tipos de informes
 * relacionados con reservas, acampantes, ocupación de parcelas e ingresos.
 */
class InformeModel extends ConectionModel {
    protected $conexion;

    /**
     * Constructor.
     * Establece la conexión a la base de datos utilizando el método heredado.
     */
    function __construct()
    {
        parent::__construct();
        $this->conexion = $this->getConection();
    }

    /**
     * Método placeholder (actualmente no implementado) para obtener informes generales.
     * 
     * @return array Arreglo vacío por ahora.
     */
    public function obtenerInformes() {
        return [];
    }

    /**
     * Obtiene la cantidad de reservas activas para una fecha o rango de fechas.
     * 
     * @param string $fecha Fecha inicial.
     * @param string|null $fecha_fin Fecha final (opcional).
     * 
     * @return string Mensaje con la cantidad de reservas encontradas.
     */
    public function obtenerReservasPorFecha($fecha, $fecha_fin) {
        $query = "SELECT COUNT(*) as total FROM reserva WHERE fecha_inicio <= ? AND fecha_fin >= ?";
        $stmt = $this->conexion->prepare($query);

        if (!empty($fecha_fin)) {
            $stmt->execute([$fecha, $fecha_fin]);
        } else {
            $stmt->execute([$fecha, $fecha]);
        }

        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        return $total > 0 ? "$total reservas encontradas." : "No se encontraron reservas para esa fecha.";
    }

    /**
     * Calcula la cantidad de personas acampando en una fecha o rango de fechas, clasificadas por edad.
     * 
     * @param string $fecha Fecha inicial.
     * @param string|null $fecha_fin Fecha final (opcional).
     * 
     * @return string Mensaje con el detalle de personas por grupo etario.
     */
    public function obtenerCantidadAcampantes($fecha, $fecha_fin) {
        $query = "SELECT 
                    SUM(menores_de_4) AS menores_4, 
                    SUM(menores_de_12) AS menores_12, 
                    SUM(mayores_de_12) AS mayores 
                  FROM reserva
                  WHERE fecha_inicio <= ? AND fecha_fin >= ? ";
        $stmt = $this->conexion->prepare($query);

        if (!empty($fecha_fin)) {
            $stmt->execute([$fecha, $fecha_fin]);
        } else {
            $stmt->execute([$fecha, $fecha]);
        }

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $data['menores_4'] + $data['menores_12'] + $data['mayores'];

        if ($total > 0) {
            return "Total: $total personas<br>Menores de 4: {$data['menores_4']}<br>Menores de 12: {$data['menores_12']}<br>Mayores de 12: {$data['mayores']}";
        } else {
            return "No hay personas acampando en la fecha seleccionada.";
        }
    }

    /**
     * Calcula el nivel de ocupación de las parcelas disponibles en una fecha o rango.
     * 
     * @param string $fecha Fecha inicial.
     * @param string|null $fecha_fin Fecha final (opcional).
     * 
     * @return string Detalle de parcelas ocupadas, libres y porcentaje de ocupación.
     */
    public function obtenerNivelOcupacion($fecha, $fecha_fin) {
        // Consultar cantidad de parcelas ocupadas
        $queryOcupadas = "SELECT COUNT(DISTINCT rp.id_parcela) as ocupadas
                          FROM reserva_parcela rp
                          JOIN reserva r ON rp.id_reserva = r.id
                          WHERE r.fecha_inicio <= ? AND r.fecha_fin >= ? ";
        $stmt = $this->conexion->prepare($queryOcupadas);

        if (!empty($fecha_fin)) {
            $stmt->execute([$fecha, $fecha_fin]);
        } else {
            $stmt->execute([$fecha, $fecha]);
        }

        $ocupadas = $stmt->fetch(PDO::FETCH_ASSOC)['ocupadas'];

        // Consultar cantidad total de parcelas disponibles
        $queryTotal = "SELECT COUNT(*) as total FROM parcela WHERE disponible = 'disponible'";
        $stmtTotal = $this->conexion->query($queryTotal);
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        if ($total == 0) return "No hay parcelas disponibles registradas.";

        $ocupacion = round(($ocupadas / $total) * 100, 2);
        $libres = $total - $ocupadas;

        return "Ocupadas: $ocupadas<br>Libres: $libres<br>Ocupación: $ocupacion%";
    }

    /**
     * Calcula el ingreso estimado generado por reservas dentro de la semana correspondiente a la fecha dada.
     * Si se proporciona $fecha_fin, se ignorará (la lógica es semanal desde $fecha).
     * 
     * @param string $fecha Fecha base (usada para calcular semana calendario).
     * @param string|null $fecha_fin Parámetro ignorado en esta función.
     * 
     * @return string Monto de ingreso estimado durante la semana.
     */
    public function obtenerIngresosSemanales($fecha, $fecha_fin = null) {
        $query = "SELECT SUM(r.precio_total) AS ganancia_semana
                  FROM reserva AS r
                  WHERE r.fecha_inicio <= DATE_ADD(?, INTERVAL (6 - WEEKDAY(?)) DAY)
                  AND r.fecha_fin >= DATE_SUB(?, INTERVAL WEEKDAY(?) DAY)";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$fecha, $fecha, $fecha, $fecha]);
        
        $ganancia = $stmt->fetch(PDO::FETCH_ASSOC)['ganancia_semana'];
        return "*aclaración: el monto considera las reservaciones sin confirmación* \n $" . number_format($ganancia, 2) . " en ingresos generados.";   
    }
}
