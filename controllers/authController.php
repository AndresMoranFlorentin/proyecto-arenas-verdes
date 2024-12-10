
<?php 
require_once './views/authView.php';
require_once './views/reservaView.php';
require_once './models/authModel.php';
require_once './helpers/sessionHelper.php';

class AuthController {
    private $view;
    private $model;
    private $helper;
    private $viewRes;

    function __construct() {
        $this->model = new AuthModel();
        $this->view = new AuthView();
        $this->helper = new SessionHelper();
        $this->viewRes = new ReservaView();
    }

    public function showLogin() {
        $this->view->showLogin();
    }

    public function showRegisForm() {
        $this->view->showRegisForm();
    }

    public function auth() {
        $usuario = $_POST['email'];
        $password = $_POST['password'];

        if (empty($usuario) || empty($password)) {
            $this->view->showLogin('Faltan completar datos');
            return;
        }


        $user = $this->model->findUser($usuario);
        if ($user && password_verify($password, $user->password)) {
            $this->helper->logIn($user);
            $logueado = $this->helper->checkUser();
            $rol = $this->helper->getRol();
            $this->viewRes->showHome($logueado, $rol);
        } else {
            $this->view->showLogin('Usuario o contraseña invalidos');
        }
    }

    function register()
    {
        if (empty($_POST['nombre']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['apellido']) || empty($_POST['localidad']) || empty($_POST['phone']) || empty($_POST['dni'])) {
            $logueado = $this->helper->checkUser();
            $mensaje = "Complete los campos";
            $this->view->renderError($mensaje);
            die();
        }
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $email = $_POST['email'];
        $localidad = $_POST['localidad'];
        $phone = $_POST['phone'];
        $dni = $_POST['dni'];
        $clave = $_POST['password'];
        $rol = 'user';
        $check = $this->model->existEmail($email);
        if ($check[0] > 0) {
            $logueado = $this->helper->checkUser();
            $mensaje = "El usuario ya existe";
            $this->view->renderError($logueado, $mensaje);
        } else {
            $userPassword = password_hash($clave, PASSWORD_BCRYPT);
            $this->model->register($nombre, $apellido, $email, $localidad, $dni, $phone, $rol, $userPassword);
            $this->helper->logIn($email);
            $logueado = $this->helper->checkUser();
            $rol = $this->helper->getRol();
            $this->view->renderHome($logueado, $rol);
           
        }
    }

    public function logout() {
        $this->helper->logOut();
        header('Location: ' . BASE_URL);    
    }
}