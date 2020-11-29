<?php 

class SesionController {

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    private function __construct() {

    }

    public function start(){
    	session_start();
    }

    public function getSesion($name){
    	return !empty($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    public function setSesion($name, $data){
    	$_SESSION[$name] = $data;
    }

    public function remove($name){
    	if(!empty($_SESSION[$name])){
    		unset($_SESSION[$name]);
    	}
    }

    public function finish(){
    	session_unset();
    	session_destroy();
    }

    public function getStatus(){
    	return session_status();
    }

    public function findAll(){
    	
    	return $_SESSION;
    }

}