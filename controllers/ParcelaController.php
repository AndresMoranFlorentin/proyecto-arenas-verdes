<?php

/**
 * un controlador pero solo creado para gestionar
 *  todo lo referemte a las parcelas
 */
require_once './models/ParcelaModel.php';
require_once './views/parcelaView.php';
class ParcelaController
{
    private $view;
    private $model;
    function __construct()
    {
        $this->model = new ParcelaModel();
        $this->view = new ParcelaView();
    }
    public function seccionParcelas()
    {
        //aqui deberia controlar que este registrado como admin
        $parcelas = $this->model->getParcelas();
        $this->view->mostrarControlParcelas($parcelas,null);
    }
    public function habilitarParcela()
    {
        $id=$_GET['id'];
        $this->model->habilitarParcela($id);
        //faltaria un pequeño control que diga si se pudo habilitar o no
        $parcelas = $this->model->getParcelas();
        $this->view->mostrarControlParcelas($parcelas,null);
    }
    public function inhabilitarParcela()
    {
        $id=$_GET['id'];
        $this->model->inhabilitarParcela($id);
        //faltaria un pequeño control que diga si se pudo habilitar o no
        $parcelas = $this->model->getParcelas();
        $this->view->mostrarControlParcelas($parcelas,null);
    }
}
