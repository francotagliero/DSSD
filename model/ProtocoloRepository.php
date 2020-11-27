<?php

class ProtocoloRepository extends PDORepository{
    private static $instance;

    public static function getInstance(){

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getProtocolos(){
        
        $protocolos = "SELECT * FROM protocolos";
        $usuarios = $this->query($query);

        return $protocolos;
    }

   
}