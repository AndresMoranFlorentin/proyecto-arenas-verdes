<?php 
class parcelaView{
    private $logueado;
    private $rol;
    
    public function mostrarControlParcelas($parcelas=null,$dispo){
        require './templates/control_parcelas.phtml';
    }
    public function parcelas($dispo=true) {
        require './templates/parcelas.phtml';
     }
}
?>