<?php
require_once "Model.php" ;

class UserModel extends Model{

    function existeUser($dni){
        $conexion = $this->conexionSQL();
        $sql="SELECT * FROM usuario WHERE dni = ?";
        $resultado=$conexion->prepare($sql);
        $resultado->execute([$dni]);
        $usuario=$resultado->fetchAll(PDO::FETCH_ASSOC);
        
        return $usuario;
    }
    function cargarNuevoUsuario($dni,$name,$apellido,$phone,$email,$location){
        $conexion = $this->conexionSQL();
        $sql="INSERT INTO usuario (dni,nombre,apellido,celular,email,localidad) 
        VALUES (?, ?, ?, ?, ?, ?)"; //nombres de las columnas de la tabla
        $conexion = $this->conexionSQL();
        $preparado = $conexion->prepare($sql);
        $preparado->execute([$dni,$name,$apellido,$phone,$email,$location]);
    }
}