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
        
        $view = new ProtocoloView();

        $protocolos = ProtocoloRepository::getInstance()->getProtocolosResponsable($this->sesion->getSesion('id_user_bd') );

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'hecho'=> $this->sesion->getSesion('id_proceso'),
            'protocolos' => $protocolos
        ));
        
    }

    public function configurarProtocolos(){
        $view = new ConfiguracionProtocolos();

        $proyectos = ProyectoRepository::getInstance()->getProyectos($this->sesion->getSesion('id_user_bd'));
        $responsables = UsuarioRepository::getInstance()->responsablesProtocolo();
        //var_dump($proyectos);
        $array_proyectos_protocolos = [];

        foreach ($proyectos as $proyecto) {
            $protocolos = ProtocoloRepository::getInstance()->getProtocolosProyecto($proyecto->getIdProyecto() );
            
            $miObjeto = array(
                'proyecto' => $proyecto,
                'protocolos' => $protocolos
            );
            $ss = (object)$miObjeto;

            $array_proyectos_protocolos[] = $ss;
        }

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'proyectos' => $array_proyectos_protocolos,
            'responsables' => $responsables
        ));
    }

    public function configurarProtocoloBD(){
        
        ProtocoloRepository::getInstance()->actualizarProtocolo($_POST);
        echo json_encode(array('valor' => 'protocoloActualizado', 'id_protocolo' => $_POST['id_protocolo'], 'id_responsable' => $_POST['id_responsable']) );
    }

    public function ejecutarProtocolo($id){

        ProtocoloRepository::getInstance()->ejecutarProtocolo($id); //cambia el estado a ejecutado, del protocoloo

        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($id);

        $idProyecto = $protocolo[0]['id_proyecto'];

        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);
        $caseId = $case[0]['case_id'];
        $uri = 'API/bpm/task?f=caseId='.$caseId;
        $request = RequestController::doTheRequest('GET', $uri);

        $taskId = $request['data'][0]->id;
        $uri = 'API/bpm/userTask/'.$taskId.'/execution';
        $request = RequestController::doTheRequest('POST', $uri);

        $view = new ProtocoloView();

        $protocolos = ProtocoloRepository::getInstance()->getProtocolos();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'hecho'=> $this->sesion->getSesion('id_proceso'),
            'protocolos' => $protocolos
        ));

    }

    public function determinarResultado($id){

        $puntaje = rand(1, 10);

        ProtocoloRepository::getInstance()->setPuntaje($id, $puntaje);

        $view = new ProtocoloView();

        $protocolos = ProtocoloRepository::getInstance()->getProtocolos();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'hecho'=> $this->sesion->getSesion('id_proceso'),
            'protocolos' => $protocolos
        ));

    }

    public function esJefe(){
        $rol = UsuarioRepository::getInstance()->getRol($this->sesion->getSesion('id_user_bd'));
        if($rol[0]['roles'] == 'jefe'){
            return true;
        }
        return  false;
    }

    public function TomarDecision(){
        $view = new ProtocoloView();

        if($this->getInstance()->esJefe()){

            $protocolos = ProtocoloRepository::getInstance()->getProtocolosDesaprobados($this->sesion->getSesion('id_user_bd'));
            $array = array('username' => $this->sesion->getSesion('user_bonita'),'protocolos' => $protocolos);

            $view->TomarDecision($array);
            
        } else {
            $view->mensaje(array('mensaje' => 'No tiene permiso'));
        }
        
    }

    public function reiniciarProtocolo($id){
        $view = new ProtocoloView();
        if($this->getInstance()->esJefe()){
            ProtocoloRepository::getInstance()->reiniciarProtocolo($id);
            $mensaje='Protocolo reiniciado.';
            $this->mostrarProtocolos($mensaje);
        } else {
            $view->mensaje(array('mensaje' => 'No tiene permiso'));
        }
    }

    public function terminarProtocolo($id){
        $view = new ProtocoloView();
        if($this->getInstance()->esJefe()){
            ProtocoloRepository::getInstance()->terminarProtocolo($id);
            $mensaje='Protocolo terminado.';
            $this->mostrarProtocolos($mensaje);
        } else {
            $view->mensaje(array('mensaje' => 'No tiene permiso'));
        }
    }

    public function mostrarProtocolos($mensaje){
        $view = new ProtocoloView();
        $protocolos = ProtocoloRepository::getInstance()->getProtocolosDesaprobados($this->sesion->getSesion('id_user_bd'));
        $array = array('username' => $this->sesion->getSesion('user_bonita'),'protocolos' => $protocolos, 'mensaje' => $mensaje);
        $view->TomarDecision($array);
    }
}

?>