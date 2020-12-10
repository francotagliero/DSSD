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
            //'hecho'=> $this->sesion->getSesion('id_proceso'),
            'rol' => $this->sesion->getSesion('rol'),
            'protocolos' => $protocolos
        ));
        
    }

    public function ejecutarProtocolo($idProtocolo){

        $client = GuzzleController::getGuzzleClient();

        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);

        $idProyecto = $protocolo[0]['id_proyecto'];

        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);

        $caseId = $case[0]['case_id'];

        $ordenProtocolo = $protocolo[0]['orden'];

        $tipoProtocolo = (int)$protocolo[0]['es_local'];

        var_dump($caseId);
        var_dump($idProtocolo);

        /*

        $uri = 'API/bpm/task?f=caseId='.$caseId;

        $request = RequestController::doTheRequest('GET', $uri);

        $taskId = $request['data'][0]->id; //EL idTask DEL DETERMINAR PROTOCOLO A EJECUTAR!!!!!*/

        $idTask = RequestController::obtenerTarea($client, $caseId);

        $idUser = RequestController::getUserIdDos($client, $this->sesion->getSesion('user_bonita') ); //idUser de bonita del usuario logeado en la appWeb

        $request = RequestController::asignarTarea($client, $idTask, $idUser); //El responsable se asigna a si mismo la DETERMINAR PROTOCOLO A EJECUTAR
        //QUE HACEMO PA?

        //MODIFICAMOS LA VARIABLE DE PROCESO tipoProtocolo (remoto=0 o local=1) y la variable cantProtocolos (=0 finaliza la instancia y notifica al jefe)

        $cantProtocolosPendiente = count(ProtocoloRepository::getInstance()->cantProtocolosProyectoPendientes($idProyecto) ); //HACER LA CONSULTA SQL PARA HACER ESTO DINAMICO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //
        $dataa = array(
            "type" => "java.lang.Integer", 
            "value" => $cantProtocolosPendiente
        );
        $responseCantProtocolos = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/cantProtocolos', $dataa);

        var_dump($cantProtocolosPendiente);
        var_dump($tipoProtocolo);
        var_dump($responseCantProtocolos);
       
        $dataTipoProtocolo = array(
            "type" => "java.lang.Integer", 
            "value" => $tipoProtocolo
        );
        $responseTipoProtocolo = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/tipoProtocolo', $dataTipoProtocolo);   
        //return $response;
        var_dump($responseTipoProtocolo);
        

        /*
        $uri = 'API/bpm/userTask/'.$idTask.'/execution';
        $request = RequestController::doTheRequest('POST', $uri);*/
        $request = RequestController::ejecutarTarea($client, $idTask);

        ProtocoloRepository::getInstance()->ejecutarProtocolo($idProtocolo); //cambia el estado a ejecutado, del protocoloo

        //Quedan para ejecutar protocolos de orden $ordenProtocolo? (estado = pendiente)
        //$cant = count(ProyectoRepository::getInstance()->actualizarOrden($idProyecto, $ordenProtocolo) );

        /*
        if($cantProtocolosPendiente > 0){
            if($cant == 0){ 
                //Actualizar el orden del proyecto
                ProyectoRepository::getInstance()->cambiarOrden($idProyecto, $ordenProtocolo + 1);
            }
        }else{
            $dataaa = array(
                "type" => "java.lang.Integer", 
                "value" => $cantProtocolosPendiente
            );
            $response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/cantProtocolos', $dataaa);
        }*/
        
        //CAMBIAMOS EL ESTADO DEL PROYECTO A EJECUTADO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        ProyectoRepository::getInstance()->cambiarEstado($idProyecto);

        $view = new ActividadView();

        //$protocolos = ProtocoloRepository::getInstance()->getProtocolos();
        $actividades = ProtocoloRepository::getInstance()->getActividades($idProtocolo);

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            //'hecho'=> $this->sesion->getSesion('id_proceso'),
            'actividades' => $actividades
        ));

    }

    public function finalizar_resolver_actividades($idProtocolo){
        ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocolo); //SETEA EL ESTADO del protocolo en "completado"!!!!!!!
        //AGREGAR UN PUNTAJE AL PROTOCOLO EN BASE A LAS ACTIVIDADES APROBADAS!!!!!!
        $cantActividades = count(ProtocoloRepository::getInstance()->getActividades($idProtocolo) );

        $mitad = $cantActividades / 2;

        $cantActividadesAprobadas = count(ProtocoloRepository::getInstance()->getActividadesAprobadas($idProtocolo) );

        if($cantActividadesAprobadas >= $mitad){
            $puntaje = random_int(7, 10);
            
        }else{
            $puntaje = random_int(1, 6);
        }
        //Modifico el puntaje del protocolo en la BD!
        ProtocoloRepository::getInstance()->setPuntajeProtocolo($idProtocolo, $puntaje);


        //TOMO LA TAREA EJECUCION LOCAL Y LA EJECUTO!
        $client = GuzzleController::getGuzzleClient();
        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);
        $idProyecto = $protocolo[0]['id_proyecto'];
        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);
        $caseId = $case[0]['case_id'];

        $idTask = RequestController::obtenerTarea($client, $caseId); //NO FUNCIONA?????????????
        $idUser = RequestController::getUserIdDos($client, $this->sesion->getSesion('user_bonita') ); //idUser de bonita del usuario logeado en la appWeb
        $request = RequestController::asignarTarea($client, $idTask, $idUser);

        //EJECUTO LA TAREA "Ejecucion Local!"
        $request = RequestController::ejecutarTarea($client, $idTask); 
        //var_dump($idTask);
        //var_dump($idUser);

        //FALTA MOSTRAR EL TEMPLATE DE DETERMINAR RESULTADO!!!!!
        $view = new DeterminarResultadoView();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'protocolos' => $protocolo
        ));

    

    }

    public function aprobarProtocolo($idProtocolo){
        //Le SETEO EL ESTADO "Terminado" al protocolo!!!!!
        ProtocoloRepository::getInstance()->aprobarProtocolo($idProtocolo); 
        $client = GuzzleController::getGuzzleClient();
        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);

        $idProyecto = $protocolo[0]['id_proyecto'];
        $ordenProtocolo = $protocolo[0]['orden'];

        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);

        $caseId = $case[0]['case_id'];

        //Volver a poner el PROYECTO en estado CONFIGURACION!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        ProyectoRepository::getInstance()->cambiarEstadoConfiguracion($idProyecto);

        //LOGICA DE Determinar resultado si haces click en "Aprobar"!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $cantProtocolosPendiente = count(ProtocoloRepository::getInstance()->cantProtocolosProyectoPendientes($idProyecto) );
        if($cantProtocolosPendiente == 0){
            //HABILITAR EL BOTON notificar Jefe en el template protocolos.html.twig! 
            //Ese responsable que ejecuta el ultimo protocolo habilita el boton notificar!


            //HAY que modificar el estado del proyecto en estado TERMINADO!!!!
            ProyectoRepository::getInstance()->cambiarEstadoTerminado($idProyecto);


            

            
        }else{
            //Si NO quedan protocolos pendientes del orden actual INCREMENTO el orden del proyecto!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $cantProtocolosPendientesOrdenActual = count(ProyectoRepository::getInstance()->actualizarOrden($idProyecto, $ordenProtocolo) );
            if($cantProtocolosPendientesOrdenActual == 0){
                ProyectoRepository::getInstance()->cambiarOrden($idProyecto, $ordenProtocolo+1);
            }
        }

        $idTask = RequestController::obtenerTarea($client, $caseId);

        $idUser = RequestController::getUserIdDos($client, $this->sesion->getSesion('user_bonita') ); //idUser de bonita del usuario logeado en la appWeb

        $request = RequestController::asignarTarea($client, $idTask, $idUser);

        //MODIFICO LA variable del proceso BONITA! resultadoProtocolo en 1 ( Aprobado = 1 )
        $resultadoProtocolo = 1;

        $datae = array(
            "type" => "java.lang.Integer", 
            "value" => $resultadoProtocolo
        );
        $response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/resultadoProtocolo', $datae);

        //EJECUTO LA TAREA BONITA "Determinar resultado"
        $request = RequestController::ejecutarTarea($client, $idTask);

        //MUESTRO LA VISTA!!!!!

        $protocolos = ProtocoloRepository::getInstance()->getProtocolosResponsable($this->sesion->getSesion('id_user_bd') );

        $view = new ProtocoloView();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            //'hecho'=> $this->sesion->getSesion('id_proceso'),
            'rol' => $this->sesion->getSesion('rol'),
            'protocolos' => $protocolos
        ));


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
            $array = array('username' => $this->sesion->getSesion('user_bonita'),'protocolos' => $protocolos,'rol' => $this->sesion->getSesion('rol'));


            $view->TomarDecision($array);
            
        } else {
            $view->mensaje(array('mensaje' => 'No tiene permiso','rol' => $this->sesion->getSesion('rol')));
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
