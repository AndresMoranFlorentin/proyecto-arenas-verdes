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
        $this->view->mostrarControlParcelas($parcelas, $logueado, $rol,BaseController::getDisponibilidad());
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
        $this->view->mostrarControlParcelas($parcelas, $logueado, $rol,BaseController::getDisponibilidad());
    }
    /**
     * Funcion encargada de marcar por parte del administrador como no disponible
     * aquellas parcelas que no pueden ser utilizadas
     */
    public function inhabilitarParcela()
    {
        $id=$_GET['id'];
        $this->model->inhabilitarParcela($id);
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        //faltaria un pequeño control que diga si se pudo habilitar o no
        $parcelas = $this->model->getParcelas();
        $this->view->mostrarControlParcelas($parcelas, $logueado, $rol,BaseController::getDisponibilidad());
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
