<?php
require_once 'BaseController.php'; // Incluir la clase abstracta
require_once './models/ParcelaModel.php';
require_once './views/parcelaView.php';
require_once './helpers/sessionHelper.php';
require_once './helpers/ToolsHelper.php';
require_once './servicios/ServicioReserva.php';


/**
 * Este archivo tiene el controlador creado para gestionar
 * todo lo referente a las parcelas
 */
class ParcelaController extends BaseController
{   
    //vista de la parcela
    private $view;
    //modelo de la parcela
    private $model;
    //istancia del ToolHelper
    private $helper;
    private $servicioR;
    private $toolHelper;

    function __construct()
    {
        $this->model = new ParcelaModel();
        $this->view = new ParcelaView();
        $this->helper = new SessionHelper();
        $this->servicioR= new ServicioReserva();
        $this->toolHelper= new ToolsHelper();

    }
    /**
     * Funcion encargada de mostrar la seccion de las parcelas
     */
    public function seccionParcelas()
    {
        //aqui deberia controlar que este registrado como admin
        $parcelas = $this->model->getParcelasConEstadoReservada();
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->mostrarControlParcelas($parcelas,null, $logueado, $rol,BaseController::getDisponibilidad());
    }
     /**
     * Funcion encargada de marcar por parte del administrador como disponible
     * aquellas parcelas que estan marcadas como inhabilitadas
     */
    public function habilitarParcela()
    {
        //$id el id de la parcela que se quiere marcar como no disponible
        $id=$_GET['id'];
        $this->model->habilitarParcela($id);
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        //faltaria un pequeño control que diga si se pudo habilitar o no
        $parcelas = $this->model->getParcelasConEstadoReservada();
        $this->view->mostrarControlParcelas($parcelas,null, $logueado, $rol,BaseController::getDisponibilidad());
    }
    /**
     * Funcion encargada de marcar por parte del administrador como no disponible
     * aquellas parcelas que no pueden ser utilizadas
     */
    public function inhabilitarParcela()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
    
        if ($logueado && $rol == 'admin') {
            $id = $_GET['id'];
            $force = isset($_GET['force']) ? $_GET['force'] : false;
    
            // Busca si existe un usuario que haya reservado esa parcela en la fecha actual
            $esta_reservada_x = $this->model->estaReservadaParcela($id);
            // Si está reservada y no se fuerza, muestra el mensaje de aviso
            if (!empty($esta_reservada_x) && !$force) {
                $parcelas = $this->model->getParcelasConEstadoReservada();
                $this->view->mostrarControlParcelas($parcelas, "reservada", $logueado, $rol, $id, BaseController::getDisponibilidad());
                return;
            }
    
            //Si se fuerza la inhabilitación, procede
            else if($force){
                $this->model->inhabilitarParcela($id);
                //se busca al usuario que realizo la reservacion a esa parcela
                //para notificarle
                $usuario=$this->servicioR->inhabilitarParcelaYReservacionRelacionada($id);
                $nombre_completo=($usuario[0]['nombre'])." ".($usuario[0]['apellido']);
                $email=$usuario[0]['email'];
                $this->toolHelper->notificarCancelacionReservaEmail($nombre_completo,$email);

                $parcelas = $this->model->getParcelasConEstadoReservada();
                $this->view->mostrarControlParcelas($parcelas, "exito", $logueado, $rol, BaseController::getDisponibilidad());
           
            }
            //en este caso no esta forzado pero es una parcela que no
            //esta reservada
                $this->model->inhabilitarParcela($id);
                $parcelas = $this->model->getParcelasConEstadoReservada();
                $this->view->mostrarControlParcelas($parcelas, "exito", $logueado, $rol, BaseController::getDisponibilidad());    
           } else {
            $this->view->mostrarSeccionPublica();
        }
    }
     /**
     * Funcion que muestra los distintos sectores de las parcelas
     * */
    public function sectoresParcelas()
    {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->parcelas($logueado, $rol, BaseController::getDisponibilidad());
    }
}
