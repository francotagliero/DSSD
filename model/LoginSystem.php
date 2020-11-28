<?php

/*
 *  LoginSystem se encarga de las funcionalidades del logueo de usuarios
 *
 */

class LoginSystem extends PDORepository {

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {

    }

    public function isLogged() {
        $session = Session::getInstance();
        return ($session->state == "logged");
    }

    public function isJefeProyecto() {
        $session = Session::getInstance();
        if (isset($session->rol[0][0])) {
            return ($session->rol[0][0] == "1");
        }else{
            return false;
        }
    }

    public function isJefeProyectoUser($user, $pass) {
        $mapper=function($row){};
        $rol = 1;
        $answer = $this->queryList("SELECT * FROM usuario WHERE username=? AND password=? AND rol_id=?;", [$user, $pass, $rol], $mapper);
        return (count($answer) > 0);
    }

    public function exist($user, $pass) {
        $mapper=function($row){};
        $answer = $this->queryList("SELECT * FROM usuario WHERE username=? AND password=?;", [$user, $pass], $mapper);
        return (count($answer) > 0);
    }


    public function isResponsableProtocolo() {
        $session = Session::getInstance();
        if (isset($session->rol[0][0])) {
            return ($session->rol[0][0] == "2");
        }else{
            return false;
        }
    }

    public function logear($user, $pass) {
        $mapper = function($row) {
            return $row['username'];
        };

        $answer = $this->queryList("SELECT * FROM usuario WHERE username=? AND password=?;", [$user, $pass], $mapper);
        if (count($answer) > 0) {
            $session = Session::getInstance();
            $session->state = "logged";
            $rol = Usuario::getInstance()->getRolUser($user);
            $session->rol = $rol;
            $session->user = $user;
            $date = date("Y-m-d H:i:s");
            $session->token = md5($date.$user);
        }
        return (count($answer) > 0);
    }

    public function login($user, $pass) {
        if ($this->exist($user,$pass)){
            if ($this->isJefeProyectoUser($user, $pass)){
                if ($this->logear($user, $pass)) {
                    return 1;
                } else {
                    return 2;
                }
            } else {
                return 3;
            }
        } else{
            return 2;
        }
    }

    public function logout() {
        Session::getInstance()->destroy();
    }

}