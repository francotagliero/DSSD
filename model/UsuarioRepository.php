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

   
}