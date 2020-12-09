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
        //ESTO ES EL DETERMINAR PROTOCOLO A EJECUTAR!!!!!!!!!!!!!!!!!!!!!!!!!!

        //$proyecto = ProyectoRepository::getInstance()->getProyecto($idProyecto);

        //$caseId = $proyecto['case_id'];

        //$idTask = RequestController::obtenerTarea($client, $caseId);

        $view = new ProtocoloView();

        $protocolos = ProtocoloRepository::getInstance()->getProtocolosResponsable($this->sesion->getSesion('id_user_bd') );

        //NECESITA TRAERME EL PROYECTO!
        //$proyecto = ProyectoRepository::getInstance()->getProyecto($protocolos[0]->getIdProyecto() ); //ACA ESTAMOS TENIENDO EN CUENTA 1 SOLO PROYECTO!
        //Despues hay que darle la posibilidad al responsable de elejir entre dos proyectos (instanciados)...y que en base a esa eleccion traer los protocolos y 
        //seguir todo el mismo flujo de ejecucion!
        //var_dump($proyecto);
        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'hecho'=> $this->sesion->getSesion('id_proceso'),
            'protocolos' => $protocolos
        ));
        
    }

    public function ejecutarProtocolo($idProtocolo){

        $client = GuzzleController::getGuzzleClient();

        ProtocoloRepository::getInstance()->ejecutarProtocolo($idProtocolo); //cambia el estado a ejecutado, del protocoloo

        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);

        $idProyecto = $protocolo[0]['id_proyecto'];

        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);

        $caseId = $case[0]['case_id'];

        /*

        $uri = 'API/bpm/task?f=caseId='.$caseId;

        $request = RequestController::doTheRequest('GET', $uri);

        $taskId = $request['data'][0]->id; //EL idTask DEL DETERMINAR PROTOCOLO A EJECUTAR!!!!!*/

        $idTask = RequestController::obtenerTarea($client, $caseId);

        $idUser = RequestController::getUserIdDos($client, $this->sesion->getSesion('user_bonita') ); //idUser de bonita del usuario logeado en la appWeb

        $request = RequestController::asignarTarea($client, $idTask, $idUser); //El responsable se asigna a si mismo la DETERMINAR PROTOCOLO A EJECUTAR
        //QUE HACEMO PA?

        //MODIFICAMOS LA VARIABLE DE PROCESO tipoProtocolo (remoto=0 o local=1) y la variable cantProtocolos (=0 finaliza la instancia y notifica al jefe)

        $tipoProtocolo = $protocolo[0]['es_local'];

        $data = array(
            "type" => "java.lang.Integer", 
            "value" => $tipoProtocolo
        );
        $response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/tipoProtocolo', $data);   
        //return $response;

        $cantProtocolos = 3;

        $dataa = array(
            "type" => "java.lang.Integer", 
            "value" => $cantProtocolos
        );
        $response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/cantProtocolos', $dataa);

        /*
        $uri = 'API/bpm/userTask/'.$idTask.'/execution';
        $request = RequestController::doTheRequest('POST', $uri);*/
        $request = RequestController::ejecutarTarea($client, $idTask);

        $view = new ActividadView();

        //$protocolos = ProtocoloRepository::getInstance()->getProtocolos();
        $actividades = ProtocoloRepository::getInstance()->getActividades($idProtocolo);

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'hecho'=> $this->sesion->getSesion('id_proceso'),
            'actividades' => $actividades
        ));

    }

    public function finalizar_resolver_actividades($idProtocolo){

    }

    public function resolverActividades($idProtocolo){
        $actividades = ProtocoloRepository::getInstance()->getActividades($idProtocolo);

        //var_dump($actividades);
        $view = new ActividadView();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'actividades' => $actividades
        ));

    }

    public function aprobarActividad($idActividad){
        $idProtocolo = ProtocoloRepository::getInstance()->getIdProtocoloByActividad($idActividad);
        
        ProtocoloRepository::getInstance()->cambiarEstadoActividad($idActividad, 'Aprobado');
        $actividades = ProtocoloRepository::getInstance()->getActividades($idProtocolo[0]);

        $view = new ActividadView();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'actividades' => $actividades,
            'mensaje' => 'Actividad Aprobada!'
        ));
    }

    public function desaprobarActividad($idActividad){
        $idProtocolo = ProtocoloRepository::getInstance()->getIdProtocoloByActividad($idActividad);

        ProtocoloRepository::getInstance()->cambiarEstadoActividad($idActividad, 'Desaprobado');
        $actividades = ProtocoloRepository::getInstance()->getActividades($idProtocolo[0]);
        $view = new ActividadView();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'actividades' => $actividades,
            'mensaje' => 'Actividad Desaprobada!'
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