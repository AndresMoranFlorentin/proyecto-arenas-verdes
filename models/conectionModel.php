<?php

class ConectionModel{
    
    protected $db;

    function __construct() {
        $user = 'root';
        $pass = '';
        $dbname = 'base_camp'; /*cambiar nombre base de datos*/
        $host = 'localhost';
        $port = '3306';
        $this->db = new PDO("mysql:host=$host:$port;dbname=$dbname", $user, $pass);
    }

    public function getConection() {
        return $this->db;
    }
}