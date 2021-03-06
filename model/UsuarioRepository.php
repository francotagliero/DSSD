<?php

class UsuarioRepository extends PDORepository{
    private static $instance;

    public static function getInstance(){

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function usuarios(){
        
        $query = "SELECT * FROM usuario";
        $usuarios = $this->query($query);

        return $usuarios;
    }

    public function loginUsuario($username, $password){
        $consulta =("SELECT * FROM usuario WHERE username=? AND password=?");

        $parametros=array($username, $password);
        $mapper = function($elemento) {
            $usuario = new Usuario(
            $elemento['id'],
            $elemento['username'],
            $elemento['password'],
            $elemento['email'],
            $elemento['is_active'],
            $elemento['roles']
            );

            return $usuario;
        };
        $lista= $this->queryList($consulta, $parametros, $mapper);

        return $lista;
    }

    public function responsablesProtocolo(){
        $consulta = "SELECT * FROM usuario WHERE roles = :roles";

        $args = array('roles' => 'responsable');

        $mapper = function($elemento) {
            $usuario = new Usuario(
            $elemento['id'],
            $elemento['username'],
            $elemento['password'],
            $elemento['email'],
            $elemento['is_active'],
            $elemento['roles']
            );

            return $usuario;
        };
        $lista= $this->queryList($consulta, $args, $mapper);

        return $lista;
    }

    public function getRol($id){
        $query = "SELECT roles FROM usuario WHERE id='$id'";
        $rol = $this->query($query);
        return $rol->fetchAll();
    }

   
}
