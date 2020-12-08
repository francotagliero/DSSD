<?php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Cookie\CookieJar;

class BaseController {

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

    public function home(){
        if(empty($this->sesion->getSesion('user_bonita') ) ){

            $view = new Login();

            $view->show(array());

        }
        else{

            $view = new Backend();
            $view->show(array(
                'rol' => $this->sesion->getSesion('rol'),
                'username' => $this->sesion->getSesion('user_bonita')
            ));
        }


    }

    public function login(){

        if(empty($_POST) ){
            $view = new Login();
            $view->show();
        }
        else{
            $usuario = $_POST["username"];
            $password = $_POST["password"];

            $resultado = UsuarioRepository::getInstance()->loginUsuario($usuario, $password);
            //var_dump($resultado);

            $cookieJar = new SessionCookieJar('MiCookie', true);
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => 'http://localhost:8080/bonita/',
                // You can set any number of default request options.
                'timeout'  => 4.0,
                'cookies' => $cookieJar
            ]);
            $resp = $client->request('POST', 'loginservice', [
                'form_params' => [
                    'username' => $usuario,
                    'password' => $password,
                    'redirect' => 'false'
                ]
            ]);

            //Almaceno el token de Bonita en una variable de sesion para utilizarla en los requests futuros
            $token = $cookieJar->getCookieByName('X-Bonita-API-Token');

            //crear LA SESION!
            $this->sesion->setSesion('user_bonita', $usuario);
            $this->sesion->setSesion('password_bonita', $password);
            $this->sesion->setSesion('base_uri_bonita', 'http://localhost:8080/bonita/');
            $this->sesion->setSesion('X-Bonita-API-Token', $token->getValue());
            $this->sesion->setSesion('id_user_bd', $resultado[0]->getId() );
            $this->sesion->setSesion('logged', true);
            $this->sesion->setSesion('rol', $resultado[0]->getRoles());

            #Busco el id del proceso a traves del nombre. 
        
            $request = $client->request('GET', 'API/bpm/process?s=Proceso',
                [
                    'headers' => [
                        'X-Bonita-API-Token' => $this->sesion->getSesion('X-Bonita-API-Token')
                ],
                
            ]);
            $tareas = $request->getBody();
    
            $response['data'] = json_decode($tareas);
       
        
            $id_proceso = $response['data'][0]->id; #Obtengo id del proceso
            $this->sesion->setSesion('id_proceso', $id_proceso);

            if($resultado[0]->getRoles() == 'jefe'){
                header("Location:./?action=proyectos");
            }else{
                header("Location:./?action=protocolos");
            }

            



        }
        
    }

    public function instanciacion(){

        $name_proyecto = $_POST["name_proyecto"];

        $cookieJar = new SessionCookieJar('MiCookie', true);
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://localhost:8080/bonita/',
            // You can set any number of default request options.
            'timeout'  => 4.0,
            'cookies' => $cookieJar
        ]);

        $id_proceso = $this->sesion->getSesion('id_proceso');
        
        $request = $client->request('POST', '/bonita/API/bpm/process/'.$id_proceso.'/instantiation',
            [
                'headers' => [
                    'X-Bonita-API-Token' => $this->sesion->getSesion('X-Bonita-API-Token')
                ],   
        ]);
        $tareas = $request->getBody();
        
        $response['data'] = json_decode($tareas);
        $caseId = $response['data']->caseId; #Obtengo id de la instancia

        //SETEO EN LA BD, para un proyecto, el idCase que corresponde!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        ProyectoRepository::getInstance()->setIdCase($name_proyecto, $caseId);

        $protocolos = ProtocoloRepository::getInstance()->getProtocolos();

        $view = new ProtocoloView();

        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'hecho'=> $caseId,
            'protocolos' => $protocolos
        ));
    }

    public function logout(){
        $this->sesion->finish();
        $view = new Login();
            $view->show(array(
                'hola' => 'hola'
            ));
    }

    public function conexion(){
        $cliente = GuzzleController::getGuzzleClient();
        echo GuzzleController::getToken();
    }

    public function cloud(){
        
        //$cliente = GuzzleController::getGuzzleClient();

        $client = new \GuzzleHttp\Client();

        $response = $client->request('post',
        "http://dssd2020.herokuapp.com/web/login/",
        array(
            'form_params' => array(
            'username' => 'lucas',
            'password' => '123456'
        )   )   );

        $obj = json_decode($response->getBody()->getContents());
        $ok = $obj->{"token"}; 

        echo $ok;
    }


    public function getProcessId(){

        /*
        $variable = 'token';

        $gateway = 1;


        $caseId = GuzzleController::doTheRequest('GET', 'API/bpm/process?s=Proceso');

        $response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/'.$caseId.'/'.$variable, $variable, $gateway);

        var_dump($response);
        */

        //$caseId=$p['data']->caseId;
        //var_dump($caseId);
        
        /*
        $client = GuzzleController::getGuzzleClient();

        $request = $client->request('GET', 'http://localhost:8080/bonita/API/bpm/process?s=Proceso',
        [
            'headers' => [
                'X-Bonita-API-Token' => GuzzleController::getToken()
            ]
        ]);

        $response = GuzzleController::doTheRequest('GET', 'API/bpm/case?p=0&c=1000');
        //return $response['data'];
        var_dump($request);
        var_dump($response);
        //var_dump();

        $client = GuzzleController::getGuzzleClient();

        $request = $client->request('GET', 'http://localhost:8080/bonita/API/bpm/process?s=Proceso',
        [
            'headers' => [
                'X-Bonita-API-Token' => GuzzleController::getToken()
            ]
        ]);
        var_dump($request->getBody());
        */
        

        $variable = 'token';

        $gateway = 1;
        $response = RequestController::doTheRequest('PUT', 'API/bpm/caseVariable/4009/'.$variable, $variable, $gateway);
        var_dump($response);

    }

    public static function login_bonita(){
        //$user = $_POST['user'];
        //$password = $_POST['password'];
        //$base_uri = $_POST['host'];

        $user = 'walter.bates';
        $password = 'bpm';
        $base_uri = 'http://localhost:8080/bonita/';

        
            //Creo una cookie jar para almacenar las cookies que me va a devolver Bonita luego del request del loginservice
            $cookieJar = new SessionCookieJar('MiCookie', true);
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $base_uri,
                // You can set any number of default request options.
                'timeout'  => 4.0,
                'cookies' => $cookieJar
            ]);
            $resp = $client->request('POST', 'loginservice', [
                'form_params' => [
                    'username' => $user,
                    'password' => $password,
                    'redirect' => 'false'
                ]
            ]);

            $aca = $client->request('GET', 'http://localhost:8080/bonita/API/bpm/process?s=Proceso');

            //Almaceno el token de Bonita en una variable de sesion para utilizarla en los requests futuros
            $token = $cookieJar->getCookieByName('X-Bonita-API-Token');

            //$_SESSION['X-Bonita-API-Token'] = $token->getValue();
            //Luego de esto, tendr�as que ver la el archivo classes/Request.php
            //Ah� vas a ver que cuando se hace un request con POST se agrega un header con el token

            //$_SESSION['user_bonita']= $_POST['user'];
            //$_SESSION['password_bonita']= $_POST['password'];
            //$_SESSION['base_uri_bonita']= $_POST['host'];
            //$_SESSION['logged'] = true;
            
            //echo $token->getValue();
            /*
            $request = $client->request('GET', 'API/bpm/process?s=Proceso',
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],
            ]);

            $tareas = $request->getBody();
            
            $json = json_decode($tareas);

            $id_proceso = $json[0]->id;

            echo $id_proceso;

            $requestt = $client->request('POST', 'API/bpm/process/'.$id_proceso.'/instantiation',
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],
            ]);

            $tareas = $requestt->getBody();
            $json = json_decode($tareas);

            $response['success'] = true;
            $response['data'] = json_decode($tareas);
            $caseId = $response['data']->caseId; #Obtengo id de la instancia

            echo $caseId;*/
            $client = GuzzleController::getGuzzleClient();

        $request = $client->request('GET', 'API/bpm/process?s=Proceso',
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],

            ]);
        $tareas = $request->getBody();
        $json = json_decode($tareas);


        $response['success'] = true;
        $response['data'] = json_decode($tareas);

        //dd($response);
        $id=$response['data'][0]->id; #Obtengo id del proceso
        //dd($id);

        #Creo una instancia del proceso
        $request = $client->request('POST', '/bonita/API/bpm/process/'.$id.'/instantiation',
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],
            ]);
        $tareas = $request->getBody();
        $json = json_decode($tareas);

        $response['success'] = true;
        $response['data'] = json_decode($tareas);
        $caseId = $response['data']->caseId; #Obtengo id de la instancia

        print_r($caseId);die(); 
    
            
            
    }

    public function backend(){
        if(empty($this->sesion->getSesion('user_bonita') ) ){

            $view = new Login();
            $view->show(array(
                'hecho'=>'Debe iniciar sesión para poder entrar a este sector.'
            ));

        }
        else{

            $view = new Backend();
            $view->show(array(
                'rol' => $this->sesion->getSesion('rol'),
                'username' => $this->sesion->getSesion('user_bonita')
            ));
        }
    }

}

?>