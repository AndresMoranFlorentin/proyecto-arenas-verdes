<?php
require_once "Model.php" ;

class UserModel extends ConectionModel{
 protected $conexion;

    function __construct()
    {
        parent::__construct();
        $this->conexion = $this->getConection();
    }
    function existeUser($dni){
        $sql="SELECT * FROM usuario WHERE dni = ?";
        $resultado=$this->conexion->prepare($sql);
        $resultado->execute([$dni]);
        $usuario=$resultado->fetchAll(PDO::FETCH_ASSOC);
        
        return $usuario;
    }
    function cargarNuevoUsuario($dni,$name,$apellido,$phone,$email,$location){
        $sql="INSERT INTO usuario (dni,nombre,apellido,celular,email,localidad) 
        VALUES (?, ?, ?, ?, ?, ?)"; //nombres de las columnas de la tabla
        $preparado =$this->conexion->prepare($sql);
        $preparado->execute([$dni,$name,$apellido,$phone,$email,$location]);
    }
}