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
        
        $query = "SELECT * FROM protocolos";
        $protocolos = $this->query($query);
        return $protocolos->fetchAll();
    }

    

   
}