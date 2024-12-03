<?php 

class AuthView {
    public function showLogin($error = null) {
        require './templates/login.phtml';
    }
    public function mostrarResultadoFormulario($mensaje=" ",$estado=""){
       // $this->render('form.phtml', ['mensaje' => $mensaje, 'tipo' => $estado]);
        require './templates/login.phtml';
    }
    private function render($template, $data = []) {
        // Extraer variables del array asociativo para que estén disponibles en la plantilla
        extract($data);

        // Incluir la plantilla HTML
        include $template;
    }
}