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

        $query = $this->db->prepare('select * FROM users WHERE email = ?');
        $query->execute([$userEmail]);
        $user = $query->fetch(PDO::FETCH_OBJ);
        return $user;
    }

    function register($nombre, $apellido, $email, $localidad, $dni, $phone, $rol, $password)
    {
        $sql = 'INSERT INTO users (nombre, apellido, email, localidad, dni, phone, rol, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$nombre, $apellido, $email, $localidad, $dni, $phone, $rol, $password]);
    }

    function existEmail($userEmail)
    {
        $query = $this->db->prepare('select COUNT(*) FROM users WHERE email = ?');
        $query->execute([$userEmail]);
        $cuenta = $query->fetch(PDO::FETCH_NUM);

        return $cuenta;
    }

    function checkRol($id)
    {
        $sql = 'select rol from users where id_usuario=?';
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$id]);
        $rol = $sentencia->fetch(PDO::FETCH_OBJ);
        return $rol;
    }

    function getUsers()
    {
        $sql = 'select * from users';
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([]);
        $usuarios = $sentencia->fetchAll(PDO::FETCH_OBJ);
        return $usuarios;
    }

    function changeRol($id, $rol)
    {
        $sql = "UPDATE users SET rol = ?
        WHERE id_usuario = ?";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$rol, $id]);
    }

    function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id_usuario = ?";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$id]);
    }

}