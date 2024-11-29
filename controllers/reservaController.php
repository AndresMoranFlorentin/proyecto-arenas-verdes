
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
        $this->view->showHome($logueado);
    }

    public function renderPrecios(){
        $logueado = $this->helper->checkUser();
        $this->view->renderPrecios($logueado);
    }

    }