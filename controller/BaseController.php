<?php
use GuzzleHttp\Client;

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
       
    }

    public function home(){
        
        $view = new Login();

        $stmt = UsuarioRepository::getInstance()->usuarios();
        
        $arr = $stmt->fetch(PDO::FETCH_ASSOC);

        var_dump($arr);

        $view->show(array(
            'hola' => $arr['username']
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


}

?>