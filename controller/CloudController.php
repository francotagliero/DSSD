<?php


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

    public function loginCloud(){
        /*
         * Me guardo el token del cloud en una variable de sesion.
         */
        $token = BaseController::getInstance()->cloud();
        $this->sesion->setSesion('tokencloud', $token);

        $client = GuzzleController::getGuzzleClient();
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

    /**public function iniciarProtocoloCloud(){

        $client = new \GuzzleHttp\Client();

        $response = RequestController::doTheRequestToCloud('get',"https://dssd2020.herokuapp.com/web/iniciar/3/proyecto/1", $client, $this->sesion->getSesion('tokencloud'));

        $client = GuzzleController::getGuzzleClient();
        $case = ProyectoRepository::getInstance()->getIdCase($idProyecto); //hay que pasar idProyecto...

        $caseId = $case[0]['case_id'];
        $idTask = RequestController::obtenerTarea($client, $caseId);
        $idUser = RequestController::getUserId($client);
        $request = RequestController::asignarTarea($client, $idTask, $idUser);
        $request = RequestController::ejecutarTarea($client, $idTask);



    } **/

    public function consultarProtocoloCloud(){

        $client = new \GuzzleHttp\Client();

        $nroProy = rand(2,3);

        $response = RequestController::doTheRequestToCloud('get',"https://dssd2020.herokuapp.com/web/consultarEstado/{$nroProy}/proyecto/1", $client, $this->sesion->getSesion('tokencloud'));
        $resultado = ((int)$response);
        echo($resultado);die;


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