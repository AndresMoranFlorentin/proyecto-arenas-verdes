<?php

require_once 'conectionModel.php';


class PassResetModel extends ConectionModel {

    protected $db;

    function __construct(){
        parent::__construct();
        $this->db = $this->getConection();
    

    }

    function create($data) {
        
        $query = $this->db->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (:user_id, :token, :expires)");
        $query->execute($data);
    }

    function findByToken($token) {
        
        $query = $this->db->prepare("SELECT * FROM password_resets WHERE token = :token");
        $query->execute([':token' => $token]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function deleteByToken($token) {
        
        $query = $this->db->prepare("DELETE FROM password_resets WHERE token = :token");
        $query->execute([':token' => $token]);
    }
}
