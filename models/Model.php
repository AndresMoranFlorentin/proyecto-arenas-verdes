<?php 
class Model{
    function conexionSQL()
    {
      $user = "root";
      $pass = "";
      $db = "campamento_municipaldb";
      $host = "localhost";
      $conexion = new PDO("mysql:host=$host;dbname=$db", $user, $pass); //conecta php con la base de datos
  
      return $conexion;
    }
}