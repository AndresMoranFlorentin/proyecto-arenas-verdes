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
        $sql = 'select rol from users where id=?';
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
        $users = $sentencia->fetchAll(PDO::FETCH_OBJ);
        return $users;
    }

    function changeRol($id, $rol)
    {
        $sql = "UPDATE users SET rol = ?
        WHERE id = ?";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$rol, $id]);
    }

    function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$id]);
    }

    function getPerfilUser($id)
    {
        $sql = 'select * from users where id = ?';
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$id]);
        $user = $sentencia->fetch(PDO::FETCH_OBJ);
        return $user;
    }

    function editarUser($nombre, $apellido, $email, $localidad, $phone, $dni, $id)
    {
        $sql = "UPDATE users SET nombre = ?, apellido = ?, email = ?, localidad = ?, phone = ?, dni = ?
        WHERE id = ?";

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$nombre, $apellido, $email, $localidad, $phone, $dni, $id]);
    }

    public function updatePassword($id, $newPassword) {
        $query = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $query->bindParam(':password', $newPassword, PDO::PARAM_STR);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }
    

}