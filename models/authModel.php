<?php

require_once 'conectionModel.php';


class AuthModel extends ConectionModel {

    protected $db;

    function __construct(){
        parent::__construct();
        $this->db = $this->getConection();
    

    }

    function findUser($userEmail)
    {

        $query = $this->db->prepare('select * FROM login WHERE email = ?');
        $query->execute([$userEmail]);
        $user = $query->fetch(PDO::FETCH_OBJ);
        return $user;
    }

    function register($nombre, $email, $rol, $password)
    {
        $sql = 'INSERT INTO login (nombre, password, email, rol) 
        VALUES (?, ?, ?, ?)';

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$nombre, $password, $email, $rol]);
    }

    function existEmail($userEmail)
    {
        $query = $this->db->prepare('select COUNT(*) FROM login WHERE email = ?');
        $query->execute([$userEmail]);
        $cuenta = $query->fetch(PDO::FETCH_NUM);

        return $cuenta;
    }

    function checkRol($id)
    {
        $sql = 'select rol from login where id_usuario=?';
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$id]);
        $rol = $sentencia->fetch(PDO::FETCH_OBJ);
        return $rol;
    }

    function getUsers()
    {
        $sql = 'select * from login';
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([]);
        $usuarios = $sentencia->fetchAll(PDO::FETCH_OBJ);
        return $usuarios;
    }

    function changeRol($id, $rol)
    {
        $sql = "UPDATE login SET rol = ?
        WHERE id_usuario = ?";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$rol, $id]);
    }

    function deleteUser($id)
    {
        $sql = "DELETE FROM login WHERE id_usuario = ?";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$id]);
    }

}