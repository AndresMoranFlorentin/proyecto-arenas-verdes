<?php

class ReservaView {
    public function showHome() {
       

require './templates/home.phtml';

    }

    public function pregFrec() {
       

        require './templates/preguntas.phtml';
        
            }
            public function reservacion() {
       

                require './templates/reservacion.phtml';
                
                    }
    }