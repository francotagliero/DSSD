<?php

class ProyectoRepository extends PDORepository{
    private static $instance;

    public static function getInstance(){

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setIdCase($nombre_proyecto, $id_case){
        
        $query = "UPDATE proyectos SET case_id = :case_id WHERE nombre = :nombre";

        $args= array('nombre' => $nombre_proyecto, 'case_id' => $id_case);

        $this->queryArgs($query, $args);
    }

   
}