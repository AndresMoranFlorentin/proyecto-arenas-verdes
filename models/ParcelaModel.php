<?php
require_once 'conectionModel.php';

class ParcelaModel extends ConectionModel{
    protected $conexion;
    function __construct()
    {
        parent::__construct();
        $this->conexion = $this->getConection();
    }
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
                        AND((NOW() BETWEEN r.fecha_inicio AND r.fecha_fin) OR (NOW() < r.fecha_inicio))
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
    function getParcelas(){
        $sql="SELECT p.* FROM parcela AS p ";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute();
        $parcelas = $resultado->fetchAll(PDO::FETCH_ASSOC);

        return $parcelas;
    }
    function estaReservadaParcela($id){
        $sql="SELECT u.* 
        FROM users AS u, reserva_parcela AS rp, reserva AS r
        WHERE (rp.id_parcela =?) 
        AND (r.id = rp.id_reserva) 
        AND((NOW() BETWEEN r.fecha_inicio AND r.fecha_fin) OR (NOW() < r.fecha_inicio))
        AND (r.id_usuario = u.id) ";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$id]);
        $usuario = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return $usuario;
    }
    function inhabilitarParcela($id){
        $sql="UPDATE parcela SET disponible='no disponible' WHERE id=? ";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$id]);
    }
    function habilitarParcela($id){
        $sql="UPDATE parcela SET disponible='disponible' WHERE id=? ";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$id]);
    }
    /**
     * Funcion encargada de eliminar la relacion entre la reserva y la parcela
     * @param int $id_reserva el id de la reserva
     * @return boolean retorna true si realizo la operacion o no
     */
    public function eliminarRelacionParcelaReserva($id_reserva){
        $sql="DELETE FROM reserva_parcela
              WHERE id_reserva = ? ";
        $conexion_bbdd=$this->conexion->prepare($sql);
        $resultado=$conexion_bbdd->execute([$id_reserva]);
        if ($resultado) {
            return true; // La consulta se ejecutó correctamente
        } else {
            return false; // Hubo un error al ejecutar la consulta
        }
    }
    /**
     * Funcion encargada de eliminar la reserva hecha
     * @param int $id_reserva el identificador unico de la reserva
     * @return boolean retorna true si pudo eliminar la reserva con exito
     */
    public function eliminarReserva($id_reserva){
        $sql="DELETE FROM reserva WHERE id = ?";
        $conexion_bbdd=$this->conexion->prepare($sql);
        $resultado=$conexion_bbdd->execute([$id_reserva]);
        if ($resultado) {
            return true; // La consulta se ejecutó correctamente
        } else {
            return false; // Hubo un error al ejecutar la consulta
        }
    }
}