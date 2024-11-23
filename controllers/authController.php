
<?php 
require_once './views/authView.php';
require_once './models/authModel.php';
require_once 'helpers/sessionHelper.php';

class AuthController {
    private $view;
    private $model;
    private $helper;

    function __construct() {
        $this->model = new AuthModel();
        $this->view = new AuthView();
        $this->helper = new SessionHelper();
    }

    public function showLogin() {
        $this->view->showLogin();
    }

    public function auth() {
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];

        if (empty($usuario) || empty($password)) {
            $this->view->showLogin('Faltan completar datos');
            return;
        }


        $user = $this->model->findUser($usuario);
        if ($user && password_verify($password, $user->password)) {
            $this->helper->iniciaSesion($user);
            
            
            header('Location: ' . BASE_URL);
        } else {
            $this->view->showLogin('Usuario o contraseña invalidos');
        }
    }

    function registrar()
    {
        if (empty($_POST['nombre']) || empty($_POST['email']) || empty($_POST['password'])) {
            $logueado = $this->helper->checkUser();
            $mensaje = "Complete los campos";
            $this->view->renderError($mensaje);
            die();
        }
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $clave = $_POST['password'];
        $rol = 'usuario';
        $check = $this->model->existEmail($email);
        if ($check[0] > 0) {
            $logueado = $this->helper->checkUser();
            $mensaje = "El usuario ya existe";
            $this->view->renderError($logueado, $mensaje);
        } else {
            $userPassword = password_hash($clave, PASSWORD_BCRYPT);
            $this->model->registrar($nombre, $email, $rol, $userPassword);
            $this->helper->iniciaSesion($nombre);
            $logueado = $this->helper->checkUser();
            $$rol = $this->helper->getRol();
            $this->view->renderHome($logueado, $rol);
        }
    }

    public function logout() {
        $this->helper->cerrarSesion();
        header('Location: ' . BASE_URL);    
    }
}