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
        if ($personas !== null) {
            $sql .= " AND p.cant_personas >= :personas";
        }
        if ($tipo_de_vehiculo !== null) {
            $sql .= " AND p.tipo_de_vehiculo LIKE :tipo_de_vehiculo";
        }
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
        if ($personas !== null) {
            $params[':personas'] = $personas;
        }
        if ($tipo_de_vehiculo !== null) {
            $params[':tipo_de_vehiculo'] = "%{$tipo_de_vehiculo}%"; // Busca el término en cualquier parte del string
        }
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
    public function analizarFalloDeReserva($fecha_inicio, $fecha_fin, $cantPersonas, $fogon, $tomaElectrica, $sombra, $agua, $con_ducha, $tipo_de_vehiculo)
    {
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
        //en caso de que no encontro parcelas en ese rango de fechas que no este reservada
        if (empty($idsFechas)) {
            $problemas[] = "No hay parcelas disponibles en ese rango de fechas.";
            //  echo "<script>console.log('".addslashes("| ...No hay parcelas disponibles en ese rango de fechas.")."');</script>";

            return $problemas;
        }

        // 2. Filtrar por capacidad
        $idsFiltradas = $this->filtrarIds($idsFechas, "cant_personas >= ?", [$cantPersonas]);

        if (empty($idsFiltradas)) {
            $problemas[] = "No hay parcelas con capacidad para $cantPersonas personas.";
            
            return $problemas;
        }
        //3. Si el tipo de vehiculo con el que ira no se encuentra disponible para las parcelas buscadas
        if ($tipo_de_vehiculo) {

            $idsFiltradas = $this->filtrarIds($idsFiltradas, "tipo_de_vehiculo LIKE ?", ["%{$tipo_de_vehiculo}%"]);

            if (empty($idsFiltradas)) {
                $problemas[] = "No se encontro parcelas que acepte ese tipo de vehiculo.";
                
                return $problemas;
            }
        }
        // 4. Filtrar por fogón
        if ($fogon) {
            $idsFiltradas = $this->filtrarIdsConServicio($idsFiltradas, "con_fogon = 1");
            if (empty($idsFiltradas)) {
                $problemas[] = "No hay parcelas con fogón.";
                
                return $problemas;

            }
        }
        // 5. Filtrar por toma eléctrica
        if ($tomaElectrica) {
            $idsFiltradas = $this->filtrarIdsConServicio($idsFiltradas, "con_toma_electrica = 1");
            if (empty($idsFiltradas)) {
                $problemas[] = "No hay parcelas con toma eléctrica.";
                
                return $problemas;
            }
        }
        // 6. Sombra
        if ($sombra) {
            $idsFiltradas = $this->filtrarIdsConServicio($idsFiltradas, "sombra = 1");
            if (empty($idsFiltradas)) {
                $problemas[] = "No hay parcelas con sombra.";
                return $problemas;
            }
        }
        // 7. Agua
        if ($agua) {
            $idsFiltradas = $this->filtrarIdsConServicio($idsFiltradas, "agua = 1");
            if (empty($idsFiltradas)) {
                $problemas[] = "No hay parcelas con conexión de agua.";
                return $problemas;
            }
        }
        // 8. Si pide Ducha
        if ($con_ducha == 1) {
            $idsFiltradas = $this->filtrarIdsConServicio($idsFiltradas, "con_ducha = 1");
            if (empty($idsFiltradas)) {
                $problemas[] = "No se encontro parcelas con ducha.";
                return $problemas;
            }
        }

        return $problemas;
    }
    /**
     * Funcion encargada de devolver array que dadas la condicion y el parametro pasado
     * devuelva un null o una parcela que si cumple con la condicion dada
     * 
     * @param array $ids contiene todos los id de las parcelas que seran evaluadas
     * @param string $condicion es la condicion que se agregara a la consulta sql
     * @return array devuelve un array o null de todos los id que pasaron por el filtro
     */
    private function filtrarIds($ids, $condicion, $params)
    {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id FROM parcela WHERE id IN ($placeholders) AND $condicion";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute(array_merge($ids, $params));
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Funcion encargada de devolver un array que dadas las condiciones de la tabla servicios
     * de si cumple o no  las parcelas devuelva un null o los id de aquellas que si cumplieron
     * 
     * @param array $ids contiene todos los id de las parcelas que seran evaluadas
     * @param string $condicion es la condicion que se agregara a la consulta sql
     * @return array devuelve un array o null de todos los id que pasaron por el filtro
     */
    private function filtrarIdsConServicio($ids, $condicion)
    {
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
    public function nuevaReserva($id_usuario, $menores_de_4, $menores_de_12, $mayores_de_12, $fecha_inicio, $fecha_fin, $tipo_vehiculo, $id_servicio, $estado, $identificador,$precio_estimado)
    {
        $sql = 'INSERT INTO reserva (id_usuario,menores_de_4,menores_de_12,mayores_de_12, fecha_inicio, fecha_fin,tipo_vehiculo, id_servicio, estado, identificador,precio_total) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([$id_usuario, $menores_de_4, $menores_de_12, $mayores_de_12, $fecha_inicio, $fecha_fin, $tipo_vehiculo, $id_servicio, $estado, $identificador,$precio_estimado]);
        // Obtener el ID de la nueva reserva
        $nuevo_id = $this->conexion->lastInsertId();
        return $nuevo_id;
    }

    /**
     * Funcion donde traera todos los precios guardados en la bbdd
     * y traera dos resultados diferentes en caso de que sea residente o no de loberia el
     * usuario(precios mas bajos para el residente de loberia)
     * 
     * @param boolean (true | false) de si es residente o no de loberia
     * @return array $precios devuelve un array de todos los precios 
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
     * Actualiza el precio de aquel servicio que tienen las parcelas
     * @param string $columna es el campo de la tabla precio a modificar
     * @param $valor es el nuevo valor que reemplazara la antigua tarifa
     * @param $residente es un valor booleano que permite filtrar si es residente de loberia o no
     */
    public function editarPrecio($columna, $valor, $residente)
    {
        $sql = "UPDATE precios SET $columna = :valor WHERE residente_local = :residente";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':valor' => $valor, ':residente' => $residente]);
    }
    /**
     * Funcion encargada de devolver todas las reservaciones mas el usuario que las creo
     * 
     * @return array $reservaciones es un array que devuelve las reservaciones junto al usuario que las creo
     */
    public function getReservacionesMasUsuario()
    {
        $sql = "SELECT CONCAT(u.nombre,' - ',u.apellido) AS nombre_completo,u.email AS email ,
              u.phone AS celular,r.id, r.fecha_inicio,r.fecha_fin, 
              r.identificador, r.estado,
              (r.menores_de_4 + r.menores_de_12 + r.mayores_de_12) AS cantidad_personas
              FROM users AS u, reserva AS r
              WHERE (u.id=r.id_usuario)";
        $conexion_bbdd = $this->conexion->prepare($sql);
        $conexion_bbdd->execute();
        $reservaciones = $conexion_bbdd->fetchAll(PDO::FETCH_ASSOC);

        return $reservaciones;
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
    public function findServicio($fogon, $tomaElectrica, $sombra, $con_ducha, $agua)
    {

        $sql = "SELECT id_servicio 
                FROM servicioreserva 
                WHERE con_fogon = ? 
                AND sombra = ? 
                AND con_toma_electrica = ? 
                AND agua = ?
                AND con_ducha = ? 
                LIMIT 1";

        try {
            $servicio = $this->conexion->prepare($sql);
            $servicio->execute([$fogon, $sombra, $tomaElectrica, $agua, $con_ducha]); // Convertimos a enteros
            $id_servicio = $servicio->fetchColumn();

            return $id_servicio;
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }
    /**
     * Funcion que sirve para traer aquel servicio con menos caracteristicas
     * 
     * @return int $id el id del servicio disponible con menos servicios 
     */
    public function getParcelaBasica()
    {
        $sql = "SELECT id_servicio 
                FROM servicioreserva 
                ORDER BY (con_fogon + sombra + con_toma_electrica + agua + poder_estacionar + con_ducha) ASC
                LIMIT 1";
        $servicio = $this->conexion->prepare($sql);
        $servicio->execute();
        $id_servicio = $servicio->fetchColumn();

        return $id_servicio;
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
    public function getReserva($id)
    {
        $sql = "SELECT r.* FROM reserva AS r WHERE r.id = ?";
        $conexion_bbdd = $this->conexion->prepare($sql);
        $conexion_bbdd->execute([$id]);
        $reserva = $conexion_bbdd->fetchAll(PDO::FETCH_ASSOC);
        return $reserva;
    }
    /**
     * Funcion encargada de eliminar la relacion entre la reserva y la parcela
     * @param int $id_reserva el id de la reserva
     * @return boolean retorna true si realizo la operacion o no
     */
    public function eliminarRelacionParcelaReserva($id_reserva)
    {
        $sql = "DELETE FROM reserva_parcela
              WHERE id_reserva = ? ";
        $conexion_bbdd = $this->conexion->prepare($sql);
        $resultado = $conexion_bbdd->execute([$id_reserva]);
        if ($resultado) {
            return true; // La consulta se ejecutó correctamente
        } else {
            return false; // Hubo un error al ejecutar la consulta
        }
    }
    /**
     * Funcion encargada de setear el estado de la reserva a confirmada
     * @param $id_reserva el id de la reservacion para confirmar
     * 
     * @return bool devuelve un true o false en caso de que actualizo o no la reserva
     */
    public function confirmarReservacion($id_reserva)
    {
        $sql = "UPDATE reserva SET estado='confirmada' WHERE id = ?";
        $conexion_bbdd = $this->conexion->prepare($sql);
        $resultado = $conexion_bbdd->execute([$id_reserva]);
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
    public function eliminarReserva($id_reserva)
    {
        $sql = "DELETE FROM reserva WHERE id = ?";
        $conexion_bbdd = $this->conexion->prepare($sql);
        $resultado = $conexion_bbdd->execute([$id_reserva]);
        if ($resultado) {
            return true; // La consulta se ejecutó correctamente
        } else {
            return false; // Hubo un error al ejecutar la consulta
        }
    }
    /**
     * Funcion que devuelve el id de la reserva que tenga el mismo id de parcela
     * y el id del usuario
     * @param int $id_parcela el id de la parcela
     * @param int $id_usuario el id del usuario
     * @return array $id_reserva el id de la reserva encontrada
     */
    function getParcelaReservada($id_parcela, $id_usuario)
    {
        $sql = "SELECT rp.id_reserva AS id
              FROM reserva AS r, users AS u, reserva_parcela AS rp
              WHERE (rp.id_parcela=? AND rp.id_reserva=r.id)
              AND (r.id_usuario = ?)";
        $resultado = $this->conexion->prepare($sql);
        $resultado->execute([$id_parcela, $id_usuario]);
        $id_reserva = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return $id_reserva;
    }
    /**
     * Funcion encargada de traer todas aquellas notificaciones
     * de aviso de finalizacion del tiempo de estadia de la 
     * reservacion
     * @return array $resultado todas las notificaciones encontradas
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
    public function getPrecioSLista()
    {
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
    //Funciones encargadas de operar con la tabla de config_tareas, obtener la ultima vez que se
    //se activaron unas funciones que deben ejecutarse dos veces al dia
    public function getUltimaEjecucion() {
        $query = $this->db->prepare("SELECT valor FROM config_tareas WHERE clave = 'ultima_ejecucion'");
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row ? $row["valor"] : null;
    }

    public function setUltimaEjecucion($fecha) {
        $query = $this->db->prepare("UPDATE config_tareas SET valor = :fecha WHERE clave = 'ultima_ejecucion'");
        $query->bindParam(":fecha", $fecha);
        $query->execute();
    }
}
