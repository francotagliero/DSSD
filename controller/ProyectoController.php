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

        if ($this->sesion->getSesion('rol') == 'responsable'){
            header("Location:./?action=protocolos");
        }

        $view = new ProyectoView();

        $view->nuevo(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'rol' => $this->sesion->getSesion('rol')
        ));
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


    public function notificarJefe($idProtocolo){
        $client = GuzzleController::getGuzzleClient();
        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);

        $idProyecto = $protocolo[0]['id_proyecto'];
        //$ordenProtocolo = $protocolo[0]['orden'];

        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);

        $caseId = $case[0]['case_id'];
        
        $idTask = RequestController::obtenerTarea($client, $caseId);
        $idUser = RequestController::getUserIdDos($client, $this->sesion->getSesion('user_bonita') ); //idUser de bonita del usuario logeado en la appWeb
        $request = RequestController::asignarTarea($client, $idTask, $idUser);

        $dataProtocolosPendientesCero = array(
            "type" => "java.lang.Integer", 
            "value" => 0
        );
        $variable = 'cantProtocolos';
        $tipoData = 'Integer';
        $data = 0;
        $response = RequestController::setCaseVariable($caseId, $variable, $data, $tipoData);
        //$response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/cantProtocolos', $dataProtocolosPendientesCero);


        $request = RequestController::ejecutarTarea($client, $idTask);

        //MODIFICAR EL ESTADO DEL PROYECTO A 'Notificado'
        ProyectoRepository::getInstance()->cambiarEstadoNotificado($idProyecto);

        //MUESTRO LA VISTA!!
        $protocolos = ProtocoloRepository::getInstance()->getProtocolosResponsable($this->sesion->getSesion('id_user_bd') );

        $view = new ProtocoloView();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'hecho'=> 'Proyecto terminado y Jefe notificado!',
            'rol' => $this->sesion->getSesion('rol'),
            'protocolos' => $protocolos
        ));


    }

    public function getProyectos(){
        $view = new ProyectoView();

        if ($this->sesion->getSesion('rol') == 'responsable'){
            header("Location:./?action=protocolos");
        }

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
            ProtocoloRepository::getInstance()->cancelarProtocolos($id);
            $mensaje='Proyecto cancelado.';
            ProtocoloController::getInstance()->mostrarProtocolos($mensaje);
        }else { $view->mensaje(array('mensaje' => 'No tiene permiso')); }
    }

    public function reiniciarProyecto($id){
        $view = new ProtocoloView();
        if(ProtocoloController::getInstance()->esJefe()){
            ProtocoloRepository::getInstance()->reiniciarProyecto($id);

            $protocolos = ProtocoloRepository::getInstance()->getProtocolosProyecto($id);
            foreach ($protocolos as $protocolo){
                ProtocoloRepository::getInstance()->reiniciarProtocolo($protocolo->getIdProtocolo());
            }
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
        $idProtocolo = ProtocoloRepository::getInstance()->altaProtocolo($_POST['nombre'], $_POST['responsable'], $_POST['fechaInicio'], $_POST['fechaFin'], $_POST['orden'], $_POST['idProyecto'], $_POST['esLocal']);

        $actividades = $_POST['actividad'];

        //var_dump($actividades);

        foreach ($actividades as $actividad) {
            ProtocoloRepository::getInstance()->altaActividad($actividad, $idProtocolo);
        }

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

    public function finalizar_configuracion($idProyecto){

        $proyecto = ProyectoRepository::getInstance()->getProyecto($idProyecto);

        $caseId = $proyecto['case_id'];
        
        
        $client = GuzzleController::getGuzzleClient();

        $idTask = RequestController::obtenerTarea($client, $caseId);

        $request = RequestController::ejecutarTarea($client, $idTask);

        $stmt = ProyectoRepository::getInstance()->getProyectos($this->sesion->getSesion('id_user_bd'));

        $view = new ProyectoView();

        $stmt = ProyectoRepository::getInstance()->getProyectos($this->sesion->getSesion('id_user_bd'));
        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'rol' => $this->sesion->getSesion('rol'),
            'proyectos' => $stmt,
            'isLogged' => $this->isLogged())
        );


    
    }

}

?>