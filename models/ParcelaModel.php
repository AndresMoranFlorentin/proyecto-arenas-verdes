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