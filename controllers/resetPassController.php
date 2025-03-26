<?php

require_once './views/authView.php';
require_once './models/authModel.php';
require_once './helpers/sessionHelper.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class PassResetController {

    private $userModel;
    private $model;
    private $helper;
    private $view;

    function __construct()
    {
        $this->model = new PassResetModel();
        $this->userModel = new AuthModel();
        $this->helper = new SessionHelper();
        $this->view = new AuthView();
    }

    public function sendRecoveryEmail() {
        $email = $_POST['email'];

        // Verificar si el correo existe en la base de datos
        $user = $this->userModel->findUser($email); // Ejemplo de método en el modelo

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Guardar el token en la base de datos
            $this->model->create([
                'user_id' => $user->id,
                'token' => $token,
                'expires' => $expires,
            ]);

            // Enviar correo
            $resetLink = "http://localhost/PPS/proyecto-arenas-verdes/newPassword/" . $token;
            try {
                // Crear una instancia de PHPMailer
                $mail = new PHPMailer(true);
    
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Servidor SMTP (puedes usar el de tu proveedor)
                $mail->SMTPAuth = true;
                $mail->Username = 'llamenza@alumnos.exa.unicen.edu.ar'; // Tu correo electrónico
                $mail->Password = 'lautaguardiola'; // Contraseña o App Password de tu cuenta
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Tipo de encriptación (TLS)
                $mail->Port = 587; // Puerto (587 para TLS)
    
                // Configuración del correo
                $mail->setFrom('llamenza@alumnos.exa.unicen.edu.ar', 'Base Campamento Arenas Verdes');
                $mail->addAddress($email); // Dirección del destinatario
    
                $mail->Subject = 'Recupera tu contraseña';
                $mail->Body = "Haz clic en este enlace para restablecer tu contraseña: $resetLink";
                
                // Enviar el correo
                $mail->send();
                echo "Revise su correo electronico para reestablecer su contraseña.";
            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
            }
        } else {
            echo "El correo no está registrado.";
        }
        
    }

    public function showResetForm($token) {
        // Verificar si el token es válido
        $reset = $this->model->findByToken($token);

        if ($reset && strtotime($reset->expires) > time()) {
            $this->view->renderPassForm($token); // Cargar la vista del formulario
        } else {
            echo "El enlace no es válido o ha expirado.";
        }
    }

    public function resetPassword() {
        $token = $_POST['token'];
        $newPassword = $_POST['password'];

        // Verificar el token
        $reset = $this->model->findByToken($token);

        if ($reset && strtotime($reset->expires) > time()) {
            // Actualizar contraseña del usuario
            $user = $this->userModel->getPerfilUser($reset->user_id);
            $this->userModel->updatePassword($user->id, password_hash($newPassword, PASSWORD_BCRYPT));


            // Eliminar el token
            $this->model->deleteByToken($token);
            $logueado = $this->helper->checkUser();
            $rol = $this->helper->getRol();
            $this->view->renderHome($logueado, $rol);
        } else {
            
            $error = "El enlace no es válido o ha expirado.";
            $this->view->renderError($error);
        }
    }
}
