<?php
require_once('BaseController.php');

class ProtocoloController{
    private static $instance;
    private $configuracion;
    private $sesion;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
       
    }

    public function getProtocolos(){
        
        $view = new Protocolo();

        $stmt = ProtocoloRepository::getInstance()->getProtocolos();
        $view->show(array('protocolos' => $stmt));
        
    }
}

?>