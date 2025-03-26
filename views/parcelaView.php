<?php 
class parcelaView{
    private $logueado;
    private $rol;
    private $parcelas;
    private $dispo;
    
    public function mostrarControlParcelas($parcelas=null,$dispo, $logueado, $rol) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        $this->parcelas = $parcelas;
        $this->dispo = $dispo;

        require './templates/control_parcelas.phtml';
    }
    public function parcelas($dispo=true, $logueado, $rol) {
        $this->dispo = $dispo;
        $this->logueado = $logueado;
        $this->rol = $rol;

        require './templates/parcelas.phtml';
     }
}
?>