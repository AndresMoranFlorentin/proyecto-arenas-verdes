<?php

require_once 'conectionModel.php';

/**
 * Clase PassResetModel
 * Gestiona las operaciones relacionadas con el restablecimiento de contraseñas.
 */
class PassResetModel extends ConectionModel {

    protected $db;

    /**
     * Constructor: establece la conexión con la base de datos heredada.
     */
    function __construct(){
        parent::__construct();
        $this->db = $this->getConection();
    }

    /**
     * Crea un nuevo registro de restablecimiento de contraseña.
     * 
     * @param array $data Datos del restablecimiento (user_id, token, expires).
     */
    function create($data) {
        
        $query = $this->db->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (:user_id, :token, :expires)");
        $query->execute($data);
    }

    /**
     * Busca un registro de restablecimiento de contraseña por token.
     * 
     * @param string $token Token del restablecimiento.
     * @return object|null Retorna el registro como objeto o null si no existe.
     */
    function findByToken($token) {
        
        $query = $this->db->prepare("SELECT * FROM password_resets WHERE token = :token");
        $query->execute([':token' => $token]);
        return $query->fetch(PDO::FETCH_OBJ); // Devuelve un objeto

    }

    /**
     * Elimina un registro de restablecimiento de contraseña por token.
     * 
     * @param string $token Token del restablecimiento.
     */
    function deleteByToken($token) {
        
        $query = $this->db->prepare("DELETE FROM password_resets WHERE token = :token");
        $query->execute([':token' => $token]);
    }
}
