<?php
require_once('BaseController.php');

class ProyectoController{
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

    public function getProyectos(){

        $view = new Proyecto();

        $stmt = ProyectoRepository::getInstance()->getProyectos();
        $view->show(array('proyectos' => $stmt, 'isLogged' => $this->isLogged()));

    }

    // Retorna true si el usuario esta logeado, false en caso contrario.
    public function isLogged() {
        return LoginSystem::getInstance()->isLogged();
    }

    public function cancelarProyecto($id) {
        $view = new ProtocoloView();
        if(ProtocoloController::getInstance()->esJefe()){
            ProyectoRepository::getInstance()->cancelarProyecto($id);
            $mensaje='Proyecto cancelado.';
            ProtocoloController::getInstance()->mostrarProtocolos($mensaje);
        }else { $view->mensaje(array('mensaje' => 'No tiene permiso')); }
    }

}

?>