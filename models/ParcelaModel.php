<?php
require_once 'conectionModel.php';

class ParcelaModel extends ConectionModel{
    protected $conexion;
    function __construct()
    {
        parent::__construct();
        $this->conexion = $this->getConection();
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
        AND (NOW() BETWEEN r.fecha_inicio AND r.fecha_fin)
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
}