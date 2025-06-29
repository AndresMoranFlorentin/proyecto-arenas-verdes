<?php 

class ReservaView
{
    // Atributos privados que almacenan el estado del usuario y datos auxiliares.
    private $logueado;          // Indica si el usuario está logueado.
    private $rol;               // Rol del usuario (admin, residente, visitante, etc.).
    private $mensaje = "";      // Mensaje de estado para mostrar en la vista.
    private $tipo_mensaje = ""; // Tipo de mensaje (error, éxito, etc.).
    private $urlPdf = "";       // URL de un PDF generado (si aplica).
    private $precios;           // Lista de precios (utilizada en vistas relacionadas).

    /**
     * Muestra la vista principal (home) del sitio.
     * 
     * @param bool $logueado  Estado de login del usuario.
     * @param string $rol     Rol del usuario.
     * @param bool $dispo    Información sobre disponibilidad de parcelas.
     */
    public function showHome($logueado, $rol, $dispo)
    {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/home.phtml';
    }

    /**
     * Renderiza la vista con la tabla de precios del camping.
     *
     * @param bool $logueado
     * @param string $rol
     * @param array $precios
     * @param bool $dispo
     */
    public function renderPrecios($logueado, $rol, $precios, $dispo)
    {
        $this->logueado = $logueado;
        $this->rol = $rol;
        $this->precios = $precios;
        require './templates/precios.phtml';
    }

    /**
     * Muestra la vista de preguntas frecuentes.
     *
     * @param string $rol
     * @param bool $logueado
     * @param bool $dispo
     */
    public function pregFrec($rol, $logueado, $dispo)
    {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/preguntas.phtml';
    }

    /**
     * Muestra la vista de información general de las parcelas.
     *
     * @param string $rol
     * @param bool $logueado
     * @param bool $dispo
     */
    public function mostrarParcela($rol, $logueado, $dispo)
    {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/parcelas.phtml';
    }

    /**
     * Muestra la vista con las parcelas disponibles para reservar.
     *
     * @param string $rol
     * @param bool $logueado
     * @param array $reservaciones
     * @param array $parcelas_por_sector
     * @param string $fechaInicio
     * @param string $fechaFin
     * @param bool $dispo
     */
    public function mostrarParcelasDisponibles($rol, $logueado, $reservaciones, $parcelas_por_sector, $fechaInicio, $fechaFin, $dispo)
    {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/parcelas.phtml';
    }

    /**
     * Muestra el precio final calculado para una parcela específica.
     *
     * @param string $rol
     * @param bool $logueado
     * @param float|null $precio_final
     * @param array $precios
     * @param mixed $dispo
     */
    public function mostrarPrecioParcela($rol, $logueado, $precio_final = null, $precios, $dispo)
    {
        $this->logueado = $logueado;
        $this->rol = $rol;
        require './templates/precios.phtml';
    }

    /**
     * Muestra la sección de reservación publica (los que no se loguearon).
     *
     * @param string $mensaje
     * @param mixed $dispo
     */
    public function seccionReservacionPublica($mensaje, $dispo)
    {
        require './templates/reservacion_publica.phtml';
    }

    /**
     * Redirige o renderiza la sección de reservación (uso interno o por usuarios logueados).
     *
     * @param string $rol
     * @param bool $logueado
     * @param int|null $id_parcela
     * @param string|null $mensaje mensaje que explica algun error o resultado de una accion como reservar
     * @param string|null $tipo_mensaje el tipo de estado del mensaje, error, exito o precaucion
     * @param bool $dispo
     * @param string|null $nombre
     * @param string|null $apellido
     * @param string|null $dni
     * @param array|null $reservaciones
     */
    public function ir_seccion_Reservacion($rol, $logueado, $id_parcela = null, $mensaje = null, $tipo_mensaje = null,$mensaje_qr=false, $dispo, $nombre = null, $apellido = null, $dni = null, $reservaciones = null)
    {
        // Se resetean los mensajes antes de cargar la vista.
        $this->mensaje = "";
        $this->tipo_mensaje = "";
        $this->logueado = $logueado;
        $this->rol = $rol;

        require './templates/reservacion.phtml';
    }
}

