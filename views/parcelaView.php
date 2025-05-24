<?php 

/**
 * Clase parcelaView
 * Encargada de renderizar vistas relacionadas con la administración y visualización de parcelas.
 */
class parcelaView {

    // Atributos privados para manejar estado de sesión y disponibilidad
    private $logueado;     // Indica si el usuario está logueado
    private $rol;          // Rol del usuario (ej. administrador, visitante)
    private $parcelas;     // Colección de parcelas (no utilizada directamente en este fragmento)
    private $dispo;        // Información sobre disponibilidad de si hay parcelas para reservar

    /**
     * Muestra la vista de control de parcelas (panel administrativo).
     * Permite visualizar las parcelas y mostrar mensajes de estado si se intenta inhabilitar una parcela.
     *
     * @param array|null $parcelas  Lista de parcelas a mostrar (opcional).
     * @param string|null $aviso    Mensaje de resultado (por ejemplo: "exito" o error).
     * @param bool $logueado        Estado de login del usuario.
     * @param string $rol           Rol del usuario (admin, user, etc.).
     * @param mixed $dispo          Información de disponibilidad de parcelas.
     */
    public function mostrarControlParcelas($parcelas = null, $aviso = null, $logueado, $rol, $dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        $this->dispo = $dispo;

        // Lógica para mostrar mensajes de éxito o error al inhabilitar una parcela
        if (!empty($aviso)) {
            if ($aviso == "exito") {
                $aviso_mensaje = "Parcela inhabilitada con éxito!!";
                $estado = "exito";
                require './templates/control_parcelas.phtml';
                die();
            } else {
                $estado = "no logrado";
                $aviso_mensaje = "Dicha Parcela se encuentra Reservada";
                require './templates/control_parcelas.phtml';
                die();
            }
        }

        // Si no hay aviso, simplemente se carga la vista
        require './templates/control_parcelas.phtml';
        die();
    }

    /**
     * Muestra la vista pública de las parcelas disponibles.
     * Esta sección está orientada a usuarios logueados o visitantes.
     *
     * @param bool $logueado  Estado de login del usuario.
     * @param string $rol     Rol del usuario.
     * @param bool $dispo     Indica si hay disponibilidad de parcelas.
     */
    public function parcelas($logueado, $rol, $dispo = true) {
        $this->dispo = $dispo;
        $this->logueado = $logueado;
        $this->rol = $rol;

        require './templates/parcelas.phtml';
    }

    /**
     * Muestra la sección pública de reservación (acceso libre sin login).
     * Usada para que cualquier usuario pueda hacer una consulta o iniciar una reserva.
     */
    public function mostrarSeccionPublica() {
        require './templates/reservacion_publica.phtml';
    }
}
?>
