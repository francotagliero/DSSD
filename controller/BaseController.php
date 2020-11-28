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

        $view->show(array(
            'hola' => $arr['username'],
            'isLogged' => $this->isLogged(),
            'tipoUsuario'=> $this->tipoUsuario()
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


    // Retorna true si el usuario esta logeado, false en caso contrario.
    public function isLogged() {
        return LoginSystem::getInstance()->isLogged();
    }

    public function tipoUsuario(){
        if ($this->isJefeProyecto()) {
            return "Jefe de proyecto";
        }else{
            return "Responsable protocolo";
        }
    }

    public function isJefeProyecto() {
        return LoginSystem::getInstance()->isJefeProyecto();
    }


    /*
    ** LOGIN:
    */
    public function login($args) {
        if (!LoginSystem::getInstance()->isLogged()) {
            $view = new Login();
            $view->show($args);
        } else {
            $this->home();
        }
    }


    public function login_system() {
        if (!LoginSystem::getInstance()->isLogged()) {
            if (isset($_POST['username']) AND isset($_POST['password'])) {
                $user = $_POST['username'];
                $pass = $_POST['password'];

                $login = LoginSystem::getInstance()->login($user, $pass);
            } else {
                $this->login(Message::getMessage(1));
            }
            switch ($login) {
                case "1": $this->home(Message::getMessage(2)); break;
                case "2": $this->login(Message::getMessage(1)); break;
                case "3": $this->login(Message::getMessage(19)); break;
                case "4": $this->login(Message::getMessage(20)); break;
                default : $this->home();;
            }
        }
    }

    public function logout_system() {
        LoginSystem::getInstance()->logout();
        $this->home(Message::getMessage(5));
    }


}

?>