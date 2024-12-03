
<?php 
require_once './views/authView.php';
require_once './models/authModel.php';
//require_once './helpers/authHelper.php';
require_once './models/UserModel.php';


class AuthController {
    private $view;
    private $model;

    function __construct() {
        $this->model = new UserModel();
        $this->view = new AuthView();
    }
    public function agregarUsuario(){
        if (
            !isset($_POST['dni'])
            && !isset($_POST['name'])
            && !isset($_POST['apellido'])
            && !isset($_POST['phone'])
            && !isset($_POST['email'])
            && !isset($_POST['location'])
        ) {
            $this->view->mostrarResultadoFormulario("Faltan datos para registrar el usuario.", "error");
        }
        else{
            $dni = $_POST['dni'];
            $name = $_POST['name'];
            $apellido = $_POST['apellido'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $location = $_POST['location'];
            //verificar que ese usuario todavia no existe 
            $usuario=$this->model->existeUser($dni);
            if($usuario!=null){
                $this->view->mostrarResultadoFormulario("El usuario con DNI $dni ya existe.", "warning");
            }
            else{
                $this->model->cargarNuevoUsuario($dni,$name,$apellido,$phone,$email,$location);
                $this->view->mostrarResultadoFormulario("¡Usuario $name $apellido registrado con éxito!", "success");
            }
        }
       
    }
    public function showLogin() {
        $this->view->showLogin();
    }

 /*   public function auth() {
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];

        if (empty($usuario) || empty($password)) {
            $this->view->showLogin('Faltan completar datos');
            return;
        }


        $user = $this->model->getByUsuario($usuario);
        if ($user && password_verify($password, $user->password)) {
            
            
            AuthHelper::login($user);
            
            header('Location: ' . BASE_URL);
        } else {
            $this->view->showLogin('Usuario inválido');
        }
    }

    public function logout() {
        AuthHelper::logout();
        header('Location: ' . BASE_URL);    
    }*/
}