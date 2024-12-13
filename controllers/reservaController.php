
<?php 
require_once './views/reservaView.php';
require_once './helpers/sessionHelper.php';

class ReservaController {
    private $view;
    private $helper;
    //private $model;

    function __construct() {
        //$this->model = new ParcelaModel();
        $this->view = new ReservaView();
        $this->helper = new SessionHelper();
    }

    public function showHome() {
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->showHome($logueado, $rol);
    }

    public function renderPrecios(){
        $logueado = $this->helper->checkUser();
        $rol = $this->helper->getRol();
        $this->view->renderPrecios($logueado, $rol);
    }

    }