
<?php 
require_once './views/reservaView.php';

class ReservaController {
    private $view;
    //private $model;

    function __construct() {
        //$this->model = new ParcelaModel();
        $this->view = new ReservaView();
    }

    public function showHome() {
        $this->view->showHome();
    }
    public function preguntasFrec() {
        $this->view->pregFrec();
    }
    public function reservacion() {
        $this->view->reservacion();
    }
    }