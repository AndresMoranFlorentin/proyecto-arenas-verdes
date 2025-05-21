<?php

class ConectionModel
{

    protected $db;

    function __construct()
    {
        $user = 'root';
        $pass = '';
        $dbname = 'campamento_municipaldb'; /*cambiar nombre base de datos*/
        $host = 'localhost';
        $port = '3306';
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=campamento_municipaldb;charset=utf8', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log('ERROR DB: ' . $e->getMessage());
            die('No se pudo conectar a la base de datos.');
        }

    }

    public function getConection()
    {
        return $this->db;
    }
}