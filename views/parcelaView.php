<?php 
class parcelaView{
    private $logueado;
    private $rol;
    private $parcelas;
    private $dispo;
    
    public function mostrarControlParcelas($parcelas=null,$aviso=null, $logueado, $rol,$dispo) {
        $this->logueado = $logueado;
        $this->rol = $rol;
        $this->parcelas = $parcelas;
        $this->dispo = $dispo;
        if(!empty($aviso)){
            if($aviso=="exito"){
                $aviso_mensaje="Parcela inhabilitada con exito!!";
                $estado="exito";
                require './templates/control_parcelas.phtml';
            }
            else{
                $estado="no logrado";
                $aviso_mensaje="Dicha Parcela se encuentra Reservada";
                require './templates/control_parcelas.phtml';
            }

        }
        require './templates/control_parcelas.phtml';
    }
    public function parcelas($logueado, $rol,$dispo=true) {
        $this->dispo = $dispo;
        $this->logueado = $logueado;
        $this->rol = $rol;

        require './templates/parcelas.phtml';
     }
     public function mostrarSeccionPublica(){
        require './templates/reservacion_publica.phtml';
     }
}
?>