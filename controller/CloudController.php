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

   
    public function loginCloudDos(Request $request){
        $idProtocolo = $request->headers['idProtocoloRemoto'];
        ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocolo);

    }

    public function loginCloud(){
        /*
         * Me guardo el token del cloud en una variable de sesion.
         */

        
        /*
        $token = BaseController::getInstance()->cloud();
        $idProtocoloRemoto = RequestController::getCaseVariable($caseId, 'idProtocoloRemoto');

        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocoloRemoto);

        $idProyecto = $protocolo[0]['id_proyecto'];

        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);

        $caseId = $case[0]['case_id'];

        RequestController::setCaseVariable($caseId, 'token', $token);

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
         * http://localhost:8080/bonita/API/bpm/task?f=caseId=9
         */
        //$idTask = RequestController::obtenerTarea($client, $caseId);

        /*
         * Busco el usuario al cual le voy asignar la tarea
         */
        //$idUser = RequestController::getUserId($client);

        /*
         * Asigno a la actividad ($idTask) el usuario que la va a ejecutar
         * http://localhost:8080/bonita/API/bpm/userTask/idTask
         */
        //$request = RequestController::asignarTarea($client, $idTask, $idUser);

        /*
         * Ejecuto la tarea idTask
         */
        //$request = RequestController::ejecutarTarea($client, $idTask);


    }

    public function iniciarProtocoloCloud(){
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
        //$idProtocoloRemoto = $this->sesion->getSesion('id_protocolo_remoto');
        //$idProtocoloRemoto = RequestController::getCaseVariable($caseId, 'idProtocoloRemoto');
        //ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo(69); //CAMBIO EL ESTADO DEL protocolo a COMPLETADO!!!!!

        //COMO INICIE EL PROTOCOLO le cambio el estado a completado!!!!!
        $idProtocoloRemoto = ProtocoloRepository::getInstance()->getUltimoProtocoloRemotoEjecutado();

        $idProtocolo = $idProtocoloRemoto[0][0];

        ProtocoloRepository::getInstance()->terminarEjecucionLocalProtocolo($idProtocolo);
        



    } 

    public function consultarProtocoloCloud(){

        $idProtocoloRemoto = ProtocoloRepository::getInstance()->getUltimoProtocoloRemotoEjecutado();

        $idProtocolo = $idProtocoloRemoto[0][0];

        //PONER UN RANDOM QUE APRUEBE Y AVACES DESAPRUEBE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //Y TENER EN CUENTA QUE SI LA variable de proceso "resultadoCloud" es igual a 0 se vuelve a ejecutar la tarea "Consultar estado protocolo"
        ProtocoloRepository::getInstance()->setPuntaje($idProtocolo, 7);//Setear un puntaje al protocolo ejecutado!!!!!

        $protocolo = ProtocoloRepository::getInstance()->getProtocolo($idProtocolo);

        $idProyecto = $protocolo[0]['id_proyecto'];

        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto);

        $caseId = $case[0]['case_id'];

        $response = RequestController::setCaseVariable($caseId, 'resultadoCloud', 1);   


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