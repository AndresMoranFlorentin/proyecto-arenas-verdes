
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

    }