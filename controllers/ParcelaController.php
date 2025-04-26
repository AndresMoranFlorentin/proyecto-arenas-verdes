<?php
require_once 'BaseController.php'; // Incluir la clase abstracta
require_once './models/ParcelaModel.php';
require_once './views/parcelaView.php';
require_once './helpers/sessionHelper.php';
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

    private $helper;

    function __construct()
    {
        $this->model = new ParcelaModel();
        $this->view = new ParcelaView();
        $this->helper = new SessionHelper();
    }
    /**
     * Funcion encargada de mostrar la seccion de las parcelas
     */
    public function seccionParcelas()
    {
        //aqui deberia controlar que este registrado como admin
        $parcelas = $this->model->getParcelas();
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
        $parcelas = $this->model->getParcelas();
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

        if($logueado && $rol=='admin'){
            $id=$_GET['id'];
            //busca si existe, el usuario que haya reservado esa parcela
            //en la fecha actual
            $esta_reservada_x=$this->model->estaReservadaParcela($id);
            if(empty($esta_reservada_x)){
                $this->model->inhabilitarParcela($id);
                $parcelas = $this->model->getParcelas();
                $this->view->mostrarControlParcelas($parcelas,"exito", $logueado, $rol,BaseController::getDisponibilidad());        
            }
            //en caso de que exista el usuario que la reservo se avisa de que la 
            //parcela ya esta reservada y si se inhabilita se perdera la
            //reservacion de dicho usuario 
            $parcelas = $this->model->getParcelas();
            $this->view->mostrarControlParcelas($parcelas,"reservada", $logueado, $rol,$id,BaseController::getDisponibilidad());        

           }
        $this->view->mostrarSeccionPublica();
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
