<?php

class ConectionModel {

    // Atributo protegido que almacenará el objeto PDO para la conexión con la base de datos.
    protected $db;

    // Constructor de la clase: establece la conexión a la base de datos al instanciar la clase.
    function __construct() {
        // Datos necesarios para conectarse a la base de datos.
        $user = 'root';                                // Nombre de usuario de la base de datos.
        $pass = '';                                    // Contraseña del usuario (vacía en este caso).
        $dbname = 'campamento_municipaldb';            // Nombre de la base de datos a utilizar.
        $host = 'localhost';                           // Dirección del servidor de base de datos.
        $port = '3306';                                // Puerto utilizado por el servidor MySQL.

        // Crea una nueva conexión PDO utilizando los datos anteriores.
        // NOTA: hay un pequeño error en la cadena de conexión, debería ser 'host=$host;port=$port'
        $this->db = new PDO("mysql:host=$host:$port;dbname=$dbname", $user, $pass);
        
        // Se recomienda agregar esta línea para lanzar excepciones en caso de error de conexión.
        // $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Método público que permite acceder a la conexión desde otras clases.
    public function getConection() {
        return $this->db;
    }
}
