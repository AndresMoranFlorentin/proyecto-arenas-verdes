<?php 
class parcelaView{
    private $logueado;
    private $rol;
    
    public function mostrarControlParcelas($parcelas=null,$dispo){
        require './templates/header.phtml';
        require './templates/control_parcelas.phtml';
        require './templates/footer.phtml';
    }
}
?>