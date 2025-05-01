<?php
require_once 'conectionModel.php';
/**
 * Este archivo realiza la conexion con la base de datos 
 * y se encarga de realizar todas aquellas operaciones 
 * relacionadas a las reservaciones
 */
class ReservaModel extends ConectionModel
{
    protected $conexion;

    function __construct()
    {
        parent::__construct();
        $this->conexion = $this->getConection();
    }
    /**
     * Busca parcelas disponibles en un camping según los filtros proporcionados.
     *
     * @param string $inicio La fecha de inicio de la reserva en formato 'YYYY-MM-DD'.
     * @param string $fecha_fin La fecha de fin de la reserva en formato 'YYYY-MM-DD'.
     * @param int $personas El número de personas que ocuparán la parcela (actualmente no se usa en la consulta SQL).
     * @param string $tipo_de_vehiculo El tipo de vehículo para la parcela (actualmente no se usa en la consulta SQL).
     * @param bool $con_fogon Indica si la parcela debe tener fogón. Puede ser null si no se requiere filtro.
     * @param bool $con_toma_electrica Indica si la parcela debe tener toma eléctrica. Puede ser null si no se requiere filtro.
     * @param bool $sombra Indica si la parcela debe tener sombra. Puede ser null si no se requiere filtro.
     * @param bool $agua Indica si la parcela debe tener suministro de agua. Puede ser null si no se requiere filtro.
     * @return array Retorna un array asociativo con las parcelas disponibles que cumplen con los filtros especificados.
     */
    public function buscarParcelasDisponibles($inicio, $fecha_fin, $personas, $tipo_de_vehiculo, $con_fogon, $con_toma_electrica, $sombra, $agua)
    {
        // Construir la consulta SQL para buscar parcelas disponibles
        $sql = "SELECT p.* 
                FROM parcela p
                LEFT JOIN servicioreserva s ON p.id_servicio = s.id_servicio
                LEFT JOIN reserva_parcela rp ON p.id = rp.id_parcela
                LEFT JOIN reserva r ON rp.id_reserva = r.id
                WHERE (
                    (
                        r.fecha_inicio NOT BETWEEN :inicio AND :fecha_fin 
                        AND r.fecha_fin NOT BETWEEN :inicio AND :fecha_fin
                        AND :inicio NOT BETWEEN r.fecha_inicio AND r.fecha_fin
                        AND :fecha_fin NOT BETWEEN r.fecha_inicio AND r.fecha_fin
                    ) 
                    OR r.id IS NULL
                AND p.disponible='disponible'
                )";

        // Agregar filtros dinámicos para las características
        if ($con_fogon !== null) {
            $sql .= " AND s.con_fogon = :con_fogon";
        }
        if ($con_toma_electrica !== null) {
            $sql .= " AND s.con_toma_electrica = :con_toma_electrica";
        }
        if ($sombra !== null) {
            $sql .= " AND s.sombra = :sombra";
        }
        if ($agua !== null) {
            $sql .= " AND s.agua = :agua";
        }

        // Preparar la consulta
        $resultado = $this->conexion->prepare($sql);

        // Asignar los parámetros
        $params = [
            ':inicio' => $inicio,
            ':fecha_fin' => $fecha_fin,
        ];
        if ($con_fogon !== null) {
            $params[':con_fogon'] = $con_fogon;
        }
        if ($con_toma_electrica !== null) {
            $params[':con_toma_electrica'] = $con_toma_electrica;
        }
        if ($sombra !== null) {
            $params[':sombra'] = $sombra;
        }
        if ($agua !== null) {
            $params[':agua'] = $agua;
        }

        // Ejecutar la consulta
        $resultado->execute($params);

        // Obtener los resultados
        $parcelas = $resultado->fetchAll(PDO::FETCH_ASSOC);

        return $parcelas;
    }
    /**
     * Busca una parcela disponible en un camping según los filtros proporcionados.
     *
     * @param string $fecha_inicio La fecha de inicio de la reserva en formato 'YYYY-MM-DD'.
     * @param string $fecha_fin La fecha de fin de la reserva en formato 'YYYY-MM-DD'.
     * @param int $cantPersonas El número de personas que ocuparán la parcela.
     * @param string $tipo_de_vehiculo El tipo de vehículo para la parcela (actualmente no se usa en la consulta SQL).
     * @param bool $fogon Indica si la parcela debe tener fogón.
     * @param bool $tomaElectrica Indica si la parcela debe tener toma eléctrica.
     * @param bool $sombra Indica si la parcela debe tener sombra.
     * @param bool $agua Indica si la parcela debe tener suministro de agua.
     * @return int Retorna el ID de la parcela disponible que cumple con los filtros especificados, o null si no hay ninguna disponible.
     */
    public function getParcelaDisponible($fecha_inicio, $fecha_fin, $cantPersonas, $tipo_de_vehiculo, $fogon, $tomaElectrica, $sombra, $agua)
    {
        $sql = "
        SELECT p.id 
        FROM parcela AS p
        INNER JOIN servicioreserva AS sr ON p.id_servicio = sr.id_servicio
        WHERE 
            (sr.con_fogon = ? AND sr.con_toma_electrica = ? AND sr.sombra = ? AND sr.agua = ?)
            AND p.cant_personas >= ?
            AND p.disponible='disponible'
            AND p.id NOT IN (
                SELECT DISTINCT reserpar.id_parcela
                FROM reserva_parcela AS reserpar
                INNER JOIN reserva AS r ON reserpar.id_reserva = r.id
                WHERE NOT (r.fecha_fin < ? OR r.fecha_inicio > ?)
            )
        LIMIT 1";

        // Ejecutar la consulta
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$fogon, $tomaElectrica, $sombra, $agua, $cantPersonas, $fecha_inicio, $fecha_fin]);
        $parcela = $resultado->fetchColumn();

        return $parcela;
    }
    /**
     * Buscara segun las condiciones dadas, porque no se ha encontrado ninguna parcela que cumpla con las condiciones pasadas por parametro
     * *
     * @param string $fecha_inicio La fecha de inicio de la reserva en formato 'YYYY-MM-DD'.
     * @param string $fecha_fin La fecha de fin de la reserva en formato 'YYYY-MM-DD'.
     * @param int $cantPersonas El número de personas que ocuparán la parcela.
     * @param string $tipo_de_vehiculo El tipo de vehículo para la parcela (actualmente no se usa en la consulta SQL).
     * @param bool $fogon Indica si la parcela debe tener fogón.
     * @param bool $tomaElectrica Indica si la parcela debe tener toma eléctrica.
     * @param bool $sombra Indica si la parcela debe tener sombra.
     * @param bool $agua Indica si la parcela debe tener suministro de agua.
     * @return array Retorna un listado de todas aquellas condiciones que no cumplio
     *   */
    public function analizarFalloDeReserva($fecha_inicio, $fecha_fin, $cantPersonas, $tipo_de_vehiculo, $fogon, $tomaElectrica, $sombra, $agua) {
        $problemas = [];
    
        // 1. ¿Hay alguna parcela disponible en ese rango de fechas?
        $sqlFechas = "
            SELECT p.id 
            FROM parcela AS p
            WHERE p.id NOT IN (
                SELECT DISTINCT reserpar.id_parcela
                FROM reserva_parcela AS reserpar
                INNER JOIN reserva AS r ON reserpar.id_reserva = r.id
                WHERE NOT (r.fecha_fin < ? OR r.fecha_inicio > ?)
            ) AND p.disponible = 'disponible'
        ";
        $stmt = $this->conexion->prepare($sqlFechas);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        $idsFechas = $stmt->fetchAll(PDO::FETCH_COLUMN);
       // var_dump("Fechas encontradas:::",$idsFechas);
        //en caso de que no encontro parcelas en ese rango de fechas que no este reservada
        if (empty($idsFechas)) {
            $problemas[] = "No hay parcelas disponibles en ese rango de fechas.";
          //  echo "<script>console.log('".addslashes("| ...No hay parcelas disponibles en ese rango de fechas.")."');</script>";

            return $problemas;
        }
       // echo "<script>console.log('".addslashes("| Hay parcelas disponibles en ese rango de fechas.")."');</script>";

        // 2. Filtrar por capacidad
        $idsFiltradas = $this->filtrarIds($idsFechas, "cant_personas >= ?", [$cantPersonas]);

        if (empty($idsFiltradas)){
            $problemas[] = "No hay parcelas con capacidad para $cantPersonas personas.";
           // echo "<script>console.log('".addslashes(" | ...No hay parcelas con capacidad para $cantPersonas personas.")."');</script>";

        } 
        //echo "<script>console.log('".addslashes(" | Hay parcelas con capacidad para $cantPersonas personas.")."');</script>";

        // 3. Filtrar por fogón
        if ($fogon) {
            $idsFiltradas = $this->filtrarIdsConServicio($idsFiltradas, "con_fogon = 1");
            if (empty($idsFiltradas)) {
                $problemas[] = "No hay parcelas con fogón.";
             //   echo "<script>console.log('".addslashes(" | ...No hay parcelas con fogón.")."');</script>";

            }
         //   echo "<script>console.log('".addslashes(" | Hay parcelas con fogón.")."');</script>";
        }
        // 4. Filtrar por toma eléctrica
        if ($tomaElectrica) {
            $idsFiltradas = $this->filtrarIdsConServicio($idsFiltradas, "con_toma_electrica = 1");
            if (empty($idsFiltradas)) {
                $problemas[] = "No hay parcelas con toma eléctrica.";
               // echo "<script>console.log('".addslashes(" | ...No hay parcelas con toma eléctrica.")."');</script>";

            }
          //  echo "<script>console.log('".addslashes(" | Hay parcelas con toma eléctrica.")."');</script>";
        }
    
        // 5. Sombra
        if ($sombra) {
            $idsFiltradas = $this->filtrarIdsConServicio($idsFiltradas, "sombra = 1");
            if (empty($idsFiltradas)){
              //  echo "<script>console.log('".addslashes(" | ...No hay parcelas con sombra. | ")."');</script>";

                $problemas[] = "No hay parcelas con sombra.";
            } 
         //   echo "<script>console.log('".addslashes(" | Hay parcelas con sombra. | ")."');</script>";

        }
    
        // 6. Agua
        if ($agua) {
            $idsFiltradas = $this->filtrarIdsConServicio($idsFiltradas, "agua = 1");
            if (empty($idsFiltradas)){
               // echo "<script>console.log('".addslashes(" | ...No hay parcelas con conexión de agua.")."');</script>";
                $problemas[] = "No hay parcelas con conexión de agua.";
            } 
         //   echo "<script>console.log('".addslashes(" | Hay parcelas con conexión de agua.")."');</script>";
        }

        return $problemas;
    }
    /**
     * Funcion encargada de devolver array que dadas la condicion y el parametro pasado
     * devuelva un null o un valor que signifique que si encontro aquella parcela que se 
     * le dio por parametro
     */
    private function filtrarIds($ids, $condicion, $params) {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id FROM parcela WHERE id IN ($placeholders) AND $condicion";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute(array_merge($ids, $params));
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    private function filtrarIdsConServicio($ids, $condicion) {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "
            SELECT p.id
            FROM parcela AS p
            INNER JOIN servicioreserva AS sr ON p.id_servicio = sr.id_servicio
            WHERE p.id IN ($placeholders) AND $condicion
        ";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Crea una nueva reserva en la base de datos.
     *
     * @param int $id_usuario El ID del usuario que realiza la reserva.
     * @param string $fecha_inicio La fecha de inicio de la reserva en formato 'YYYY-MM-DD'.
     * @param string $fecha_fin La fecha de fin de la reserva en formato 'YYYY-MM-DD'.
     * @param string $tipo_de_vehiculo El tipo de vehículo asociado con la reserva.
     * @param int $id_servicio El ID del servicio asociado con la reserva.
     * @param string $estado El estado de la reserva ('confirmada', 'pendiente').
     * @param string $identificador Un identificador único para la reserva.
     *return El ID de la nueva reserva creada en la base de datos.
     */
    public function nuevaReserva($id_usuario,$menores_de_4,$menores_de_12,$mayores_de_12, $fecha_inicio, $fecha_fin,$tipo_vehiculo, $id_servicio, $estado, $identificador)
    {
        $sql = 'INSERT INTO reserva (id_usuario,menores_de_4,menores_de_12,mayores_de_12, fecha_inicio, fecha_fin,tipo_vehiculo, id_servicio, estado, identificador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([$id_usuario,$menores_de_4,$menores_de_12,$mayores_de_12, $fecha_inicio, $fecha_fin,$tipo_vehiculo, $id_servicio, $estado, $identificador]);
        // Obtener el ID de la nueva reserva
        $nuevo_id = $this->conexion->lastInsertId();
        return $nuevo_id;
    }

    /**
     * Funcion donde traera todos los precios guardados en la bbdd
     * @param boolean (true | false) de si es residente o no de loberia
     */
    public function getPrecios($residente)
    {
        $sql = "SELECT p.* FROM precios AS p WHERE p.residente_local = ? ";
        // Ejecutar la consulta
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$residente]);
        $precios = $resultado->fetchAll(PDO::FETCH_ASSOC);

        return $precios;
    }
        /**
     * Actualizar el precio de una parcela
     */
    public function editarPrecio($columna, $valor, $residente) {
        $sql = "UPDATE precios SET $columna = :valor WHERE residente_local = :residente";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':valor' => $valor, ':residente' => $residente]);
    }
    
    
    /**
     * Busca el ID de un servicio de reserva que cumpla con las características especificadas.
     *
     * @param bool $fogon Indica si el servicio debe incluir un fogón.
     * @param bool $tomaElectrica Indica si el servicio debe incluir toma eléctrica.
     * @param bool $sombra Indica si el servicio debe incluir sombra.
     * @param bool $agua Indica si el servicio debe incluir suministro de agua.
     * @return int|null Retorna el ID del servicio que cumple con los filtros especificados, o null si no se encuentra ningún servicio.
     * @throws PDOException Si ocurre un error durante la consulta a la base de datos.
     */
    public function findServicio($fogon, $tomaElectrica, $sombra, $agua)
    {

        $sql = "SELECT id_servicio 
                FROM servicioreserva 
                WHERE con_fogon = ? 
                AND sombra = ? 
                AND con_toma_electrica = ? 
                AND agua = ? 
                LIMIT 1;";

        try {
            $servicio = $this->conexion->prepare($sql);
            $servicio->execute([$fogon, $sombra, $tomaElectrica, $agua]); // Convertimos a enteros
            $id_servicio = $servicio->fetchColumn();
            //echo "<script>console.log('".addslashes("id del servicio original:::-> ".$id_servicio)."');</script>";

            return $id_servicio;
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }
    /**
     * Funcion que agrega un nuevo servicio adicional
     * @param bool $fogon Indica si la parcela debe tener fogón.
     * @param bool $tomaElectrica Indica si la parcela debe tener toma eléctrica.
     * @param bool $sombra Indica si la parcela debe tener sombra.
     * @param bool $agua Indica si la parcela debe tener suministro de agua.
     * 
    */
    public function insertServicioAdicional($fogon, $tomaElectrica, $sombra, $agua)
    {
        $sql = 'INSERT INTO servicioreserva (con_fogon,con_toma_electrica,sombra,agua) VALUES (?, ?, ?, ?)';
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([$fogon, $sombra, $tomaElectrica, $agua]);
    }
    /**
     * Funcion encargada de crear la relacion entre la parcela y una reserva generada
     * un nuevo registro en la tabla  de parcela_reserva
     * @param int $id_nueva_reserva id de la reserva
     * @param int $id_parcela id de la parcela 
     */
    public function crearRelacionParcela($id_nueva_reserva, $id_parcela)
    {
        $sql = "INSERT INTO reserva_parcela (id_reserva, id_parcela) VALUES (?, ?)";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([$id_nueva_reserva, $id_parcela]);
    }
    /**
     * Funcion encargada de devolver aquella reserva que tenga el mismo id
     * que recibe por parametro
     * @param int $id id de la reservacio
     * @return array devuelve aquella reserva que encontro
     */
    public function getReserva($id){
        $sql="SELECT r.* FROM reserva AS r WHERE r.id = ?";
        $conexion_bbdd=$this->conexion->prepare($sql);
        $conexion_bbdd->execute([$id]);
        $reserva=$conexion_bbdd->fetchAll(PDO::FETCH_ASSOC);
        return $reserva;
    }
    /**
     * Funcion encargada de eliminar la relacion entre la reserva y la parcela
     * @param int $id_reserva el id de la reserva
     * @return boolean retorna true si realizo la operacion o no
     */
    public function eliminarRelacionParcelaReserva($id_reserva){
        $sql="DELETE FROM reserva_parcela
              WHERE id_reserva = ? ";
        $conexion_bbdd=$this->conexion->prepare($sql);
        $resultado=$conexion_bbdd->execute([$id_reserva]);
        if ($resultado) {
            return true; // La consulta se ejecutó correctamente
        } else {
            return false; // Hubo un error al ejecutar la consulta
        }
    }
    /**
     * Funcion encargada de eliminar la reserva hecha
     * @param int $id_reserva el identificador unico de la reserva
     * @return boolean retorna true si pudo eliminar la reserva con exito
     */
    public function eliminarReserva($id_reserva){
        $sql="DELETE FROM reserva WHERE id = ?";
        $conexion_bbdd=$this->conexion->prepare($sql);
        $resultado=$conexion_bbdd->execute([$id_reserva]);
        if ($resultado) {
            return true; // La consulta se ejecutó correctamente
        } else {
            return false; // Hubo un error al ejecutar la consulta
        }
    }
    function getParcelaReservada($id_parcela, $id_usuario){
        $sql="SELECT rp.id_reserva AS id
              FROM reserva AS r, users AS u, reserva_parcela AS rp
              WHERE (rp.id_parcela=? AND rp.id_reserva=r.id)
              AND (r.id_usuario = ?)";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$id_parcela,$id_usuario]);
        $id_reserva = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return $id_reserva;
    }
    /**
     * Funcion encargada de traer todas aquellas notificaciones
     * de aviso de finalizacion del tiempo de estadia de la 
     * reservacion
     *  */    
    public function getNotificaciones()
    {        
        $sql = "SELECT DISTINCT np.*
         FROM notificaciones_pendientes np
         WHERE np.enviado = 0 
         AND DATE(fecha_notificacion) = CURDATE()";
        $servicio = $this->conexion->prepare($sql);
        $servicio->execute();
        $resultado = $servicio->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }
    /**
     * Funcion que devuelve si hay disponibilidad de reservas
     * si hay mas o menos que el limite pasado
     * @param int $limite es el numero minimo de parcelas encontradas que debe haber para
     * que se considere que ahy disponibilidad
     * @return boolean devuelve true o false si hay disponibilidad
     */
    public function hayDisponibilidad($limite)
    { 
        $sql = "SELECT COUNT(p.id) AS total
                FROM parcela AS p
                LEFT JOIN reserva_parcela AS rp ON p.id = rp.id_parcela
                WHERE rp.id_parcela IS NULL";
        $servicio = $this->conexion->prepare($sql);
        $servicio->execute();
        $resultado = $servicio->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > $limite ? true : false;
    }
    /**
     * Funcion encargada de borrar aquella notificacion que reciba como parametro su id
     * @param int $id id de la notificacion que se debe borrar
     */
    public function deleteNotificacion($id)
    {
        $sql = "DELETE FROM notificaciones_pendientes WHERE id = ?";
        $servicio = $this->conexion->prepare($sql);
        $servicio->execute([$id]);
    }
    /**
     * Verifica si una parcela está reservada en el momento actual.
     *
     * @param int $id_p El ID de la parcela a verificar.
     * @return bool Retorna true si la parcela está reservada actualmente, false en caso contrario.
     */
    public function estaReservada($id_p)
    {
        $sql = "SELECT COUNT(rp.id_parcela)
              FROM reserva_parcela AS rp, reserva AS r
              ON(rp.id_reserva=r.id)
              WHERE (rp.id_parcela=?)
              AND NOW() BETWEEN r.fecha_inicio AND r.fecha_fin";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$id_p]);
        $resultado = $consulta->fetchColumn(PDO::FETCH_ASSOC);
        return $resultado > 0 ? true : false;
    }
    /**
     * Marca una parcela como no disponible.
     *
     * @param int $id_p El ID de la parcela a marcar como no disponible.
     * @return void
     */
    public function marcarNoDisponibleParcela($id_p)
    {
        $sql = "UPDATE parcela SET disponible='no disponible' WHERE id=?";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$id_p]);
    }
 /*Se encarga de buscar las reservas del usuario filtrando por idUsuario*/
public function obtenerReservasDelUsuario($id_usuario)
{
    $sql = "SELECT * FROM reserva WHERE id_usuario = ?";
    $consulta = $this->conexion->prepare($sql);
    $consulta->execute([$id_usuario]);
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}
 /**
     * Obtener la lista de precios
     */
    public function getPrecioSLista() {
        $sql = "SELECT * FROM precios WHERE residente_local = 1";
        $stmtResidentes = $this->conexion->prepare($sql);
        $stmtResidentes->execute();
        $residentes = $stmtResidentes->fetch(PDO::FETCH_ASSOC);

        $sqlNoResidentes = "SELECT * FROM precios WHERE residente_local = 0";
        $stmtNoResidentes = $this->conexion->prepare($sqlNoResidentes);
        $stmtNoResidentes->execute();
        $noResidentes = $stmtNoResidentes->fetch(PDO::FETCH_ASSOC);

        return [
            'residentes' => $residentes,
            'no_residentes' => $noResidentes,
        ];
    }


}