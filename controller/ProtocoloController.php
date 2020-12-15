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
        //var_dump($this->sesion->getSesion('tokencloud') );

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

        //var_dump($caseId);
        //var_dump($idProtocolo);

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
        
        /*
        $dataa = array(
            "type" => "java.lang.Integer", 
            "value" => $cantProtocolosPendiente
        );
        $responseCantProtocolos = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/cantProtocolos', $dataa);
        */
        //var_dump($cantProtocolosPendiente);
        //var_dump($tipoProtocolo);
        //var_dump($responseCantProtocolos);
       
        /*
        $dataTipoProtocolo = array(
            "type" => "java.lang.Integer", 
            "value" => $tipoProtocolo
        );
        $responseTipoProtocolo = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/tipoProtocolo', $dataTipoProtocolo);   
        //return $response;
        var_dump($responseTipoProtocolo);
        */
        $response = RequestController::setCaseVariable($caseId, 'tipoProtocolo', $tipoProtocolo);
        $response = RequestController::setCaseVariable($caseId, 'cantProtocolos', $cantProtocolosPendiente);


        /*
        $uri = 'API/bpm/userTask/'.$idTask.'/execution';
        $request = RequestController::doTheRequest('POST', $uri);*/

        

        ProtocoloRepository::getInstance()->ejecutarProtocolo($idProtocolo); //cambia el estado a ejecutado, del protocoloo

        //Quedan para ejecutar protocolos de orden $ordenProtocolo? (estado = pendiente)
        //$cant = count(ProyectoRepository::getInstance()->actualizarOrden($idProyecto, $ordenProtocolo) );

        //CAMBIAMOS EL ESTADO DEL PROYECTO A EJECUTADO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        ProyectoRepository::getInstance()->cambiarEstado($idProyecto);

        //SI ES LOCAL EL PROTOCOLO!!
       if($tipoProtocolo == 1){
            $request = RequestController::ejecutarTarea($client, $idTask);
            $view = new ActividadView();

            //$protocolos = ProtocoloRepository::getInstance()->getProtocolos();
            $actividades = ProtocoloRepository::getInstance()->getActividades($idProtocolo);

            $view->show(array(
                'username' => $this->sesion->getSesion('user_bonita'),
                //'hecho'=> $this->sesion->getSesion('id_proceso'),
                'actividades' => $actividades
            ));
       }else{
            //Agregar el id del protocolo remoto a la variable de proceso Bonita "idProtocoloRemoto"
            //$this->sesion->setSesion('id_protocolo_remoto', $idProtocolo);
            $response = RequestController::setCaseVariable($caseId, 'idProtocoloRemoto', $idProtocolo);
            //var_dump(RequestController::getCaseVariable($caseId, 'idProtocoloRemoto') );
            //$this->sesion->setSesion('case_id', $caseId);

            //AGREGAR EN LA BD el id del protocolo REMOTO que va a ser ejecutado por el CLOUD!!!!! asi desde CloudController puedo acceder al idProtocolo
            //ProtocoloRepository::getInstance()->setProtocoloRemotoEjecutado($idProtocolo);
            
            //AGREGA EL PROTOCOLO REMOTO A LA TABLA protocolos ejecutados (SOLO REMOTOS)
            //$protocolo = ProtocoloRepository::getInstance()->getUltimoProtocoloRemotoEjecutado();
            //var_dump($protocolo[0][0]);
            $request = RequestController::ejecutarTarea($client, $idTask);//EMPIEZA A EJECUTAR EL PROCESO BONITAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
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
          
        
        

        
    }

    public function determinarRemoto($idProtocolo){
        //$idProtocoloRemoto = $this->sesion->getSesion('id_protocolo_remoto');

        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);

        $view = new DeterminarResultadoView();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'protocolos' => $protocolo
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

        $idTask = RequestController::obtenerTarea($client, $caseId);

        $idUser = RequestController::getUserIdDos($client, $this->sesion->getSesion('user_bonita') ); //idUser de bonita del usuario logeado en la appWeb

        $request = RequestController::asignarTarea($client, $idTask, $idUser);

        //Volver a poner el PROYECTO en estado CONFIGURACION!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        ProyectoRepository::getInstance()->cambiarEstadoConfiguracion($idProyecto);

        //LOGICA DE Determinar resultado si haces click en "Aprobar"!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $cantProtocolosPendiente = count(ProtocoloRepository::getInstance()->cantProtocolosProyectoPendientes($idProyecto) );
        if($cantProtocolosPendiente == 0){
            //HABILITAR EL BOTON notificar Jefe en el template protocolos.html.twig! 
            //Ese responsable que ejecuta el ultimo protocolo habilita el boton notificar!


            //$cantProtocolosPendiente = count(ProtocoloRepository::getInstance()->cantProtocolosProyecto($idProyecto) );
            //MODIFICAR LA VARIABLE DE PROCESO cantProtocolos!
            
            /*
            $dataaa = array(
                "type" => "java.lang.Integer", 
                "value" => 0
            );
            $response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/cantProtocolos', $dataaa);*/

            $response = RequestController::setCaseVariable($caseId, 'cantProtocolos', 0);
            //HAY que modificar el estado del proyecto en estado TERMINADO!!!!
            
            ProyectoRepository::getInstance()->cambiarEstadoTerminado($idProyecto);


            

            
        }else{
            //Si NO quedan protocolos pendientes del orden actual INCREMENTO el orden del proyecto!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $cantProtocolosPendientesOrdenActual = count(ProyectoRepository::getInstance()->actualizarOrden($idProyecto, $ordenProtocolo) );
            if($cantProtocolosPendientesOrdenActual == 0){
                ProyectoRepository::getInstance()->cambiarOrden($idProyecto, $ordenProtocolo+1);
            }
        }

        //MODIFICO LA variable del proceso BONITA! resultadoProtocolo en 1 ( Aprobado = 1 )
        $resultadoProtocolo = 1;

        /*
        $datae = array(
            "type" => "java.lang.Integer", 
            "value" => $resultadoProtocolo
        );
        $response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/resultadoProtocolo', $datae);*/

        $response = RequestController::setCaseVariable($caseId, 'resultadoProtocolo', $resultadoProtocolo);

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

    public function desaprobarProtocolo($idProtocolo){
        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);

        $client = GuzzleController::getGuzzleClient();
        $idProyecto = $protocolo[0]['id_proyecto'];
        $ordenProtocolo = $protocolo[0]['orden'];
        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);
        $caseId = $case[0]['case_id'];

        //CAMBIAR EL ESTADO DEL PROYECTO A "tomar_decision"
        ProyectoRepository::getInstance()->cambiarEstadoTomarDecision($idProyecto);
        //CAMBIAR EL ESTADO DEL PROTOCOLO A "desaprobado"
        ProtocoloRepository::getInstance()->desaprobarProtocolo($idProtocolo);


        //CAMBIE EL VALOR de la variable de proceso resultadoProtocolo a 0 
        $resultadoProtocolo = 0;
        $response = RequestController::setCaseVariable($caseId, 'resultadoProtocolo', $resultadoProtocolo);

        //AVANZAR EN EL PROCESO BONITA!
        $idTask = RequestController::obtenerTarea($client, $caseId);

        $idUser = RequestController::getUserIdDos($client, $this->sesion->getSesion('user_bonita') ); //idUser de bonita del usuario logeado en la appWeb

        $request = RequestController::asignarTarea($client, $idTask, $idUser);

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

    public function tomarDecision($idProyecto){
        $view = new ProtocoloView();

        if($this->getInstance()->esJefe() ){

            //$protocolos = ProtocoloRepository::getInstance()->getProtocolosDesaprobados($this->sesion->getSesion('id_user_bd'));
            $protocolos = ProtocoloRepository::getInstance()->getProtocolosDesaprobadosDos($idProyecto);

            $array = array('username' => $this->sesion->getSesion('user_bonita'),'protocolos' => $protocolos,'rol' => $this->sesion->getSesion('rol') );


            $view->tomarDecision($array);
            
        } else {
            $view->mensaje(array('mensaje' => 'No tiene permiso','rol' => $this->sesion->getSesion('rol')));
        }
        
    }

    public function reiniciarProtocolo($idProtocolo){
        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);
        if($this->getInstance()->esJefe()){
            if($protocolo[0]['es_local'] == 1 ){
                    /*
                     * Si el protocolo es local, ademas de reiniciar el protocolo tiene que reiniciar las actividades
                     */
                    ProtocoloRepository::getInstance()->reiniciarProtocolo($idProtocolo); //cambia el estado del protocolo
                }else{
                    /*
                     * Si es remoto, el protocolo no tiene actividades por ende, solo reinicia el protocolo.
                     */
                    ProtocoloRepository::getInstance()->reiniciarProtocoloSinActividades($idProtocolo);
                }
            /*
             * ejecuto la tarea, actualizo la variable de proceso bonita y muestro la vista de proyectos.
             */
            $mensaje='Protocolo reiniciado.';

            //Y HAY QUE CAMBIAR EL ESTADO DEL PROYECTO A "configuracion"!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            ProyectoRepository::getInstance()->cambiarEstadoConfiguracion($protocolo[0]['id_proyecto']);

            ProyectoController::getInstance()->tomarDecisionAction($protocolo[0]['id_proyecto'], $mensaje, 0);
        } else {
            $view->mensaje(array('mensaje' => 'No tiene permiso'));
        }
    }

    public function terminarProtocolo($idProtocolo){

        if($this->getInstance()->esJefe()){
            $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);
            $proyecto = ProyectoRepository::getInstance()->getProyecto($protocolo[0]['id_proyecto']);


            ProtocoloRepository::getInstance()->terminarProtocolo($idProtocolo); //SETEO al protocolo en estado "terminado"



            $cantProtocolosPendiente = count(ProtocoloRepository::getInstance()->cantProtocolosProyectoPendientes($protocolo[0]['id_proyecto']) );
            if($cantProtocolosPendiente == 0){
                $case = ProyectoRepository::getInstance()->getIdCase($protocolo[0]['id_proyecto']);
                $caseId = $case[0]['case_id'];
                $response = RequestController::setCaseVariable($caseId, 'cantProtocolos', 0);
                $idProyecto = $protocolo[0]['id_proyecto'];

                ProyectoRepository::getInstance()->cambiarEstadoTerminado($idProyecto);
            }else{
                /*
                 * Si NO quedan protocolos pendientes del orden actual INCREMENTO el orden del proyecto.
                 */

                //Y HAY QUE CAMBIAR EL ESTADO DEL PROYECTO A "configuracion"!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                $idProyecto = $protocolo[0]['id_proyecto'];
                $ordenProtocolo = $protocolo[0]['orden'];
                ProyectoRepository::getInstance()->cambiarEstadoConfiguracion($protocolo[0]['id_proyecto']);

                $cantProtocolosPendientesOrdenActual = count(ProyectoRepository::getInstance()->actualizarOrden($idProyecto, $ordenProtocolo) );
                if($cantProtocolosPendientesOrdenActual == 0){
                    ProyectoRepository::getInstance()->cambiarOrden($idProyecto, $ordenProtocolo+1);
                }
        }

            /*
             * ejecuto la tarea, actualizo la variable de proceso bonita y muestro la vista de proyectos.
             */
            $mensaje='Protocolo terminado.';
            

            ProyectoController::getInstance()->tomarDecisionAction($protocolo[0]['id_proyecto'], $mensaje, 0);

        } else {
            $view->mensaje(array('mensaje' => 'No tiene permiso'));
        }
    }

    public function mostrarProtocolos($mensaje){
        $view = new ProtocoloView();

        $protocolos = ProtocoloRepository::getInstance()->getProtocolosDesaprobados($this->sesion->getSesion('id_user_bd'));

        $array = array('username' => $this->sesion->getSesion('user_bonita'),'protocolos' => $protocolos, 'mensaje' => $mensaje);
        $view->tomarDecision($array);
    }
}

?>
