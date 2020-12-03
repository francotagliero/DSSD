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
        $this->sesion = SesionController::getInstance();
    }

    // Retorna true si el usuario esta logeado, false en caso contrario.
    public function isLogged() {
        return $this->sesion->getSesion('logged');
    }

    public function nuevoProyecto(){
        $view = new ProyectoView();

        $view->nuevo(array());
    }

    public function nuevoProyectoAction(){
        if ($this->isLogged()) {

            $client = GuzzleController::getGuzzleClient();

            $idProceso = $this->sesion->getSesion('id_proceso');

            /*
             * Instancio el proceso y obtengo el case_id del nuevo proyecto
             * http://localhost:8080/bonita/API/bpm/process/5149425974540291037/instantiation
            */
            $caseId = RequestController::instanciarProceso($client, $idProceso);

            /*
             * Guardo el proyecto junto con el case_id unico por cada proyecto
             */
            ProyectoRepository::getInstance()->altaProyecto($this->sesion->getSesion('id_user_bd'), $_POST['nombre'], $_POST['fechaInicio'], $_POST['fechaFin'],$caseId);

            /*
             * Obtengo la tarea actual del proyecto
             * http://localhost:8080/bonita/API/bpm/task?f=caseId=9
             */
            $idTask = RequestController::obtenerTarea($client, $caseId);

            /*
             * Busco el usuario al cual le voy asignar la tarea
             */
            $idUser = RequestController::getUserId($client);

            /*
             * Asigno a la actividad ($idTask) el usuario que la va a ejecutar
             * http://localhost:8080/bonita/API/bpm/userTask/idTask
             */
            $request = RequestController::asignarTarea($client, $idTask, $idUser);

            $this->getProyectos();
        } else {
            BaseController::getInstance()->home();
        }
    }

    public function getProyectos(){
        $view = new ProyectoView();

        $stmt = ProyectoRepository::getInstance()->getProyectos($this->sesion->getSesion('id_user_bd'));
        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'rol' => $this->sesion->getSesion('rol'),
            'proyectos' => $stmt,
            'isLogged' => $this->isLogged())
        );

    }

    public function cancelarProyecto($id) {
        $view = new ProtocoloView();
        if(ProtocoloController::getInstance()->esJefe()){
            ProyectoRepository::getInstance()->cancelarProyecto($id);
            $mensaje='Proyecto cancelado.';
            ProtocoloController::getInstance()->mostrarProtocolos($mensaje);
        } else {
            $view->mensaje(array('mensaje' => 'No tiene permiso'));
        }
    }

    public function reiniciarProyecto($id){
        $view = new ProtocoloView();
        if(ProtocoloController::getInstance()->esJefe()){
            ProtocoloRepository::getInstance()->reiniciarProyecto($id);
            $mensaje='Proyecto reiniciado.';
            ProtocoloController::getInstance()->mostrarProtocolos($mensaje);
        } else {
            $view->mensaje(array('mensaje' => 'No tiene permiso'));
        }
    }

    public function agregarProtocolo($idProyecto){
        $view = new ProyectoView();

        $proyecto = ProyectoRepository::getInstance()->getProyecto($idProyecto);
        $protocolos = ProtocoloRepository::getInstance()->getProtocolosProyecto($idProyecto);
        $usuarios = UsuarioRepository::getInstance()->usuarios();

        $view->agregarProtocolo(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'rol' => $this->sesion->getSesion('rol'),
            'proyecto' => $proyecto,
            'protocolos' => $protocolos,
            'usuarios' => $usuarios,
            'id_proyecto' => $idProyecto,
            'isLogged' => $this->isLogged()
        ));
    }

    public function agregarProtocoloAction(){

        $view = new ProyectoView();

        /*
         * Guardo el protocolo
         */
        ProtocoloRepository::getInstance()->altaProtocolo($_POST['nombre'], $_POST['responsable'], $_POST['fechaInicio'], $_POST['fechaFin'], $_POST['idProyecto'], $_POST['esLocal']);


        $proyecto = ProyectoRepository::getInstance()->getProyecto($_POST['idProyecto']);
        $protocolos = ProtocoloRepository::getInstance()->getProtocolosProyecto($_POST['idProyecto']);
        $usuarios = UsuarioRepository::getInstance()->usuarios();

        $view->agregarProtocolo(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'rol' => $this->sesion->getSesion('rol'),
            'proyecto' => $proyecto,
            'protocolos' => $protocolos,
            'usuarios' => $usuarios,
            'isLogged' => $this->isLogged()
        ));
    }

}

?>