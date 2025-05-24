<?php
require_once 'conectionModel.php';

/**
 * Clase ParcelaModel
 * Gestiona las operaciones relacionadas con las parcelas del camping y su relación con las reservas.
 */
class ParcelaModel extends ConectionModel {
    protected $conexion;

    /**
     * Constructor: establece la conexión con la base de datos heredada de ConectionModel.
     */
    function __construct()
    {
        parent::__construct();
        $this->conexion = $this->getConection();
    }

    /**
     * Obtiene todas las parcelas, añadiendo una columna extra 'reservada' con valor 1 o 0
     * que indica si la parcela está reservada actualmente o lo estará en el futuro.
     * 
     * @return array Arreglo asociativo con datos de cada parcela y su estado de reserva.
     */
    function getParcelasConEstadoReservada() {
        $sql = "
            SELECT 
                p.*, 
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM reserva_parcela AS rp
                        INNER JOIN reserva AS r 
                            ON rp.id_reserva = r.id
                        WHERE rp.id_parcela = p.id
                        AND ((NOW() BETWEEN r.fecha_inicio AND r.fecha_fin) OR (NOW() < r.fecha_inicio))
                    ) THEN 1
                    ELSE 0
                END AS reservada
            FROM parcela AS p
        ";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute();
        $parcelas = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return $parcelas;
    }

    /**
     * Obtiene todas las parcelas de la base de datos sin información adicional de reserva.
     * 
     * @return array Arreglo asociativo con todas las parcelas.
     */
    function getParcelas(){
        $sql = "SELECT p.* FROM parcela AS p";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute();
        $parcelas = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return $parcelas;
    }

    /**
     * Verifica si una parcela específica está actualmente reservada o lo estará.
     * Devuelve los datos del/los usuario(s) que tienen la reserva.
     * 
     * @param int $id ID de la parcela a verificar.
     * @return array Arreglo de usuarios con reservas asociadas a la parcela.
     */
    function estaReservadaParcela($id){
        $sql = "
            SELECT u.* 
            FROM users AS u, reserva_parcela AS rp, reserva AS r
            WHERE rp.id_parcela = ? 
              AND r.id = rp.id_reserva 
              AND ((NOW() BETWEEN r.fecha_inicio AND r.fecha_fin) OR (NOW() < r.fecha_inicio))
              AND r.id_usuario = u.id
        ";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$id]);
        $usuario = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return $usuario;
    }

    /**
     * Marca una parcela como 'no disponible' en la base de datos.
     * 
     * @param int $id ID de la parcela a inhabilitar.
     */
    function inhabilitarParcela($id){
        $sql = "UPDATE parcela SET disponible = 'no disponible' WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$id]);
    }

    /**
     * Marca una parcela como 'disponible' en la base de datos.
     * 
     * @param int $id ID de la parcela a habilitar.
     */
    function habilitarParcela($id){
        $sql = "UPDATE parcela SET disponible = 'disponible' WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$id]);
    }

    /**
     * Elimina la relación entre una reserva y una parcela en la tabla intermedia 'reserva_parcela'.
     * 
     * @param int $id_reserva ID de la reserva cuya relación con la parcela se eliminará.
     * @return boolean Retorna true si la operación fue exitosa, false si hubo un error.
     */
    public function eliminarRelacionParcelaReserva($id_reserva){
        $sql = "DELETE FROM reserva_parcela WHERE id_reserva = ?";
        $conexion_bbdd = $this->conexion->prepare($sql);
        $resultado = $conexion_bbdd->execute([$id_reserva]);
        return $resultado;
    }

    /**
     * Elimina completamente una reserva de la tabla 'reserva'.
     * 
     * @param int $id_reserva ID de la reserva a eliminar.
     * @return boolean Retorna true si la reserva fue eliminada correctamente, false si ocurrió un error.
     */
    public function eliminarReserva($id_reserva){
        $sql = "DELETE FROM reserva WHERE id = ?";
        $conexion_bbdd = $this->conexion->prepare($sql);
        $resultado = $conexion_bbdd->execute([$id_reserva]);
        return $resultado;
    }
}
