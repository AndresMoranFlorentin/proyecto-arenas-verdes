<?php 
class parcelaView{
    private $logueado;
    private $rol;
    private $parcelas;
    private $dispo;
    
    public function mostrarControlParcelas($parcelas=null, $logueado, $rol,$dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        $this->parcelas = $parcelas;
        $this->dispo = $dispo;

        require './templates/control_parcelas.phtml';
    }
    public function parcelas($logueado, $rol,$dispo=true) {
        $this->dispo = $dispo;
        $this->logueado = $logueado;
        $this->rol = $rol;

        require './templates/parcelas.phtml';
     }
}
?>