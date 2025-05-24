<?php

require_once 'conectionModel.php';

/**
 * Clase AuthModel
 * Gestiona las operaciones relacionadas con autenticación, registro de usuarios y roles.
 */
class AuthModel extends ConectionModel {

    protected $db;

    /**
     * Constructor: establece la conexión con la base de datos heredada.
     */
    function __construct(){
        parent::__construct();
        $this->db = $this->getConection();
    }

    /**
     * Busca un usuario por su dirección de email.
     * 
     * @param string $userEmail Email del usuario a buscar.
     * @return object|null Retorna el usuario como objeto o null si no existe.
     */
    function findUser($userEmail)
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $query->execute([$userEmail]);
        $user = $query->fetch(PDO::FETCH_OBJ);
        return $user;
    }

    /**
     * Busca un usuario por su ID.
     * 
     * @param int $id ID del usuario.
     * @return array|null Retorna los datos del usuario en forma de array asociativo.
     */
    function findUserById($id)
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $query->execute([$id]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    /**
     * Busca un usuario por su DNI.
     * 
     * @param string $dni DNI del usuario.
     * @return object|null Retorna el usuario como objeto.
     */
    function findUserByDni($dni)
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE dni = ?');
        $query->execute([$dni]);
        $user = $query->fetch(PDO::FETCH_OBJ);
        return $user;
    }

    /**
     * Verifica si un usuario con el ID dado existe.
     * (Este método es idéntico a findUserById, podría refactorizarse).
     * 
     * @param int $id_user ID del usuario.
     * @return object|null Retorna el usuario como objeto.
     */
    function userIsResident($id_user)
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $query->execute([$id_user]);
        $user = $query->fetch(PDO::FETCH_OBJ);
        return $user;
    }

    /**
     * Registra un nuevo usuario en la base de datos.
     * 
     * @param string $nombre
     * @param string $apellido
     * @param string $email
     * @param string $localidad
     * @param string $dni
     * @param string $phone
     * @param string $rol
     * @param string $password Contraseña ya hasheada.
     */
    function register($nombre, $apellido, $email, $localidad, $dni, $phone, $rol, $password)
    {
        $sql = 'INSERT INTO users (nombre, apellido, email, localidad, dni, phone, rol, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$nombre, $apellido, $email, $localidad, $dni, $phone, $rol, $password]);
    }

    /**
     * Verifica si ya existe un usuario con el email especificado.
     * 
     * @param string $userEmail Email a verificar.
     * @return array Retorna un array con la cantidad de coincidencias encontradas.
     */
    function existEmail($userEmail)
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $query->execute([$userEmail]);
        $cuenta = $query->fetch(PDO::FETCH_NUM);
        return $cuenta;
    }

    /**
     * Verifica si ya existe un usuario con el DNI especificado.
     * 
     * @param string $dni DNI a verificar.
     * @return array Retorna un array con la cantidad de coincidencias encontradas.
     */
    function existDni($dni)
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM users WHERE dni = ?');
        $query->execute([$dni]);
        $cuenta = $query->fetch(PDO::FETCH_NUM);
        return $cuenta;
    }

    /**
     * Obtiene el rol de un usuario según su ID.
     * 
     * @param int $id ID del usuario.
     * @return object|null Retorna un objeto con el campo 'rol'.
     */
    function checkRol($id)
    {
        $sql = 'SELECT rol FROM users WHERE id = ?';
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$id]);
        $rol = $sentencia->fetch(PDO::FETCH_OBJ);
        return $rol;
    }

    /**
     * Obtiene todos los usuarios registrados.
     * 
     * @return array Retorna un array de objetos usuario.
     */
    function getUsers()
    {
        $sql = 'SELECT * FROM users';
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([]);
        $users = $sentencia->fetchAll(PDO::FETCH_OBJ);
        return $users;
    }

    /**
     * Cambia el rol de un usuario específico.
     * 
     * @param int $id ID del usuario.
     * @param string $rol Nuevo rol a asignar.
     */
    function changeRol($id, $rol)
    {
        $sql = "UPDATE users SET rol = ? WHERE id = ?";
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$rol, $id]);
    }

    /**
     * Elimina un usuario de la base de datos.
     * 
     * @param int $id ID del usuario a eliminar.
     */
    function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$id]);
    }

    /**
     * Obtiene el perfil completo de un usuario por su ID.
     * 
     * @param int $id ID del usuario.
     * @return object|null Objeto con los datos del usuario.
     */
    function getPerfilUser($id)
    {
        $sql = 'SELECT * FROM users WHERE id = ?';
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$id]);
        $user = $sentencia->fetch(PDO::FETCH_OBJ);
        return $user;
    }

    /**
     * Actualiza los datos de perfil de un usuario.
     * 
     * @param string $nombre
     * @param string $apellido
     * @param string $email
     * @param string $localidad
     * @param string $phone
     * @param string $dni
     * @param int $id ID del usuario a modificar.
     */
    function editarUser($nombre, $apellido, $email, $localidad, $phone, $dni, $id)
    {
        $sql = "UPDATE users SET nombre = ?, apellido = ?, email = ?, localidad = ?, phone = ?, dni = ?
                WHERE id = ?";
        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([$nombre, $apellido, $email, $localidad, $phone, $dni, $id]);
    }

    /**
     * Actualiza la contraseña de un usuario.
     * 
     * @param int $id ID del usuario.
     * @param string $newPassword Nueva contraseña ya hasheada.
     */
    public function updatePassword($id, $newPassword) {
        $query = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $query->bindParam(':password', $newPassword, PDO::PARAM_STR);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }

}
