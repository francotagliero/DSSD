<?php

class ProtocoloController{
    private static $instance;
    
    private $sesion;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->sesion = SesionController::getInstance();
    }

    public function getProtocolos(){
        
        $view = new Protocolo();

        $protocolos = ProtocoloRepository::getInstance()->getProtocolos();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'hecho'=> $this->sesion->getSesion('id_proceso'),
            'protocolos' => $protocolos
        ));
        
    }

    public function configurarProtocolos(){
        $view = new ConfiguracionProtocolos();

        $proyecto = ProyectoRepository::getInstance()->getIdProyecto($this->sesion->getSesion('id_user_bd'));

        $protocolos = ProtocoloRepository::getInstance()->getProtocolos();
        
        
        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'hecho'=> $proyecto[0]->getIdProyecto(),
            'protocolos' => $protocolos
        ));
    }

}

?>