<?php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CloudController{
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

   
    public function loginCloudDos(){
        $token = BaseController::getInstance()->cloud();
        //echo($token);die;
       
        $client = new \GuzzleHttp\Client();

        $nroProy = rand(2,3);

        $response = RequestController::doTheRequestToCloud('get',"https://dssd2020.herokuapp.com/web/consultarEstado/{$nroProy}/proyecto/1", $client, $token);
        $resultado = ((int)$response);
        //var_dump($response);
        echo $response;

    }

    public function loginCloud($idProtocoloRemoto){
        /*
         * Me guardo el token del cloud en una variable de sesion.
         */

        $token = BaseController::getInstance()->cloud();
        //$idProtocoloRemoto = RequestController::getCaseVariable($caseId, 'idProtocoloRemoto');

        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocoloRemoto);

        $idProyecto = $protocolo[0]['id_proyecto'];

        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);

        $caseId = $case[0]['case_id'];
        
        //$response = RequestController::setCaseVariable($caseId, 'idProtocoloRemoto', 1); OK 
        $response = RequestController::setCaseVariableString($caseId, 'token', $token);
        //$response = RequestController::setCaseVariable($caseId, 'caseIdProyecto', $token);  

        //$this->sesion->setSesion('tokencloud', $token);*/
        
        
        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo(10);
        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocoloRemoto[0][0]);
        //API/bpm/caseVariable/:caseId/:variableName
        //$client = GuzzleController::getGuzzleClient();
        //$case = ProyectoRepository::getInstance()->getIdCase($idProyecto); //hay que pasar idProyecto...
        //var_dump($this->sesion->getSesion('tokencloud'));
        //$caseId = $case[0]['case_id'];
        /*
         * Obtengo la tarea actual del proyecto
         * http://localhost:12310/bonita/API/bpm/task?f=caseId=9
         */
        //$idTask = RequestController::obtenerTarea($client, $caseId);

        /*
         * Busco el usuario al cual le voy asignar la tarea
         */
        //$idUser = RequestController::getUserId($client);

        /*
         * Asigno a la actividad ($idTask) el usuario que la va a ejecutar
         * http://localhost:12310/bonita/API/bpm/userTask/idTask
         */
        //$request = RequestController::asignarTarea($client, $idTask, $idUser);

        /*
         * Ejecuto la tarea idTask
         */
        //$request = RequestController::ejecutarTarea($client, $idTask  );
        //print_r($_POST);

        //$data = json_decode(file_get_contents('php://input'),1);
        //print_r($data);



        //$idProtocoloRemoto = $_POST["FirstName"];
        //foreach (getallheaders() as $valueR => $valor) {
            //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($valueR);
            //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($valor);
        //}
        //return $idProtocoloRemoto;
        //$idProtocoloRemoto = $this->sesion->getSesion('id_protocolo_remoto');
        ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocoloRemoto);

        
        //
        //$client = new \GuzzleHttp\Client();

        //$nroProy = rand(2,3);

        //$response = RequestController::doTheRequestToCloud('get',"https://dssd2020.herokuapp.com/web/consultarEstado/{$nroProy}/proyecto/1", $client, $token);
        //$resultado = ((int)$response);
        //$response = RequestController::setCaseVariable($caseId, 'resultadoCloud', $resultado);
        
    }

    public function iniciarProtocoloCloud($idProtocolo){
        ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocolo);

        /*
        $client = new \GuzzleHttp\Client();

        $response = RequestController::doTheRequestToCloud('get',"https://dssd2020.herokuapp.com/web/iniciar/3/proyecto/1", $client, $this->sesion->getSesion('tokencloud'));

        $client = GuzzleController::getGuzzleClient();
        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto); //hay que pasar idProyecto...

        $caseId = $case[0]['case_id'];
        $idTask = RequestController::obtenerTarea($client, $caseId);
        $idUser = RequestController::getUserId($client);
        $request = RequestController::asignarTarea($client, $idTask, $idUser);
        $request = RequestController::ejecutarTarea($client, $idTask);
        */
        
        //$idProtocoloRemoto = RequestController::getCaseVariable($caseId, 'idProtocoloRemoto');
        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo(69); //CAMBIO EL ESTADO DEL protocolo a COMPLETADO!!!!!

        //COMO INICIE EL PROTOCOLO le cambio el estado a completado!!!!!
        //$idProtocoloRemoto = ProtocoloRepository::getInstance()->getUltimoProtocoloRemotoEjecutado();

        //$idProtocolo = $idProtocoloRemoto[0][0];

        //$idProtocolo = RequestController::getCaseVariable($caseId, 'idProtocoloRemoto') 
        //$idProtocoloRemoto = $_POST["n"];
        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocoloRemoto);

        //print_r($_POST);

        //$data = json_decode(file_get_contents('php://input'),1);
        //print_r($data);



        //$idProtocoloRemoto = $_POST["FirstName"];
        //foreach (getallheaders() as $valueR => $valor) {
           // ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($valueR);
            //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($valor);
        //}
        
        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo(1);
        



    } 

    public function consultarProtocoloCloud($idProtocolo){
        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocolo);
        /*
        foreach (getallheaders() as $valueR => $valor) {
            ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($valueR);
            ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($valor);
            ProtocoloRepository::getInstance()->setPuntaje($idProtocolo, 7);//Setear un puntaje al protocolo ejecutado!!!!!

            $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);

            //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocolo);

            $idProyecto = $protocolo[0]['id_proyecto'];

            $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);

            $caseId = $case[0]['case_id'];

            $response = RequestController::setCaseVariable($caseId, 'resultadoCloud', 1);  
        }/*

        //$idProtocoloRemoto = ProtocoloRepository::getInstance()->getUltimoProtocoloRemotoEjecutado();
        
        //$idProtocolo = $idProtocoloRemoto[0][0];
        //$idProtocoloRemoto = $_POST["id_remoto"];
        //$idProtocolo = $idProtocoloRemoto;

        //PONER UN RANDOM QUE APRUEBE Y AVACES DESAPRUEBE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //Y TENER EN CUENTA QUE SI LA variable de proceso "resultadoCloud" es igual a 0 se vuelve a ejecutar la tarea "Consultar estado protocolo"*/

        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);

        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocolo);

        $idProyecto = $protocolo[0]['id_proyecto'];

        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);

        $caseId = $case[0]['case_id'];

        //get getCaseVariable

        
        $client = new \GuzzleHttp\Client();

        $token = RequestController::getCaseVariable($caseId, 'token');

        $nroProtocolo = rand(2,3);

        $response = RequestController::doTheRequestToCloud('get',"https://dssd2020.herokuapp.com/web/consultarEstado/{$nroProtocolo}/proyecto/1", $client, $token);
        $resultado = ((int)$response); //CON ESTO...DE TODO EL STRING te quedas solo con el numero?.......



        ProtocoloRepository::getInstance()->setPuntaje($idProtocolo, $resultado);//Setear un puntaje al protocolo ejecutado!!!!!



        $response = RequestController::setCaseVariable($caseId, 'resultadoCloud', 1); 

        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocolo);


        //$response = RequestController::setCaseVariable($caseId, 'resultadoCloud', $resultado);  

        //print_r($_POST);

        ////print_r($data);



        //$idProtocoloRemoto = $_POST["FirstName"];
        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocolo);


        //PARA EL PUNTAJE RANDOM!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        /*
        $client = new \GuzzleHttp\Client();

        $nroProy = rand(2,3);

        $response = RequestController::doTheRequestToCloud('get',"https://dssd2020.herokuapp.com/web/consultarEstado/{$nroProy}/proyecto/1", $client, $this->sesion->getSesion('tokencloud'));
        $resultado = ((int)$response);
        echo($resultado);die;*/


        /*
         * Avanzar proceso bonita y actualizar la variable.  
        $client = GuzzleController::getGuzzleClient();
        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto); //hay que pasar idProyecto...

        $caseId = $case[0]['case_id'];
        $idTask = RequestController::obtenerTarea($client, $caseId);
        $idUser = RequestController::getUserId($client);
        $request = RequestController::asignarTarea($client, $idTask, $idUser);

        $tipoProtocolo = $protocolo[0]['es_local'];

        $data = array(
            "type" => "java.lang.Integer", 
            "value" => $resultado
        );
        $response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/tipoProtocolo', $data);

        $request = RequestController::ejecutarTarea($client, $idTask);

        */


    }

}