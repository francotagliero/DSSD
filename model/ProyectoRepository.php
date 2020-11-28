<?php

class ProyectoRepository extends PDORepository{
    private static $instance;

    public static function getInstance(){

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getProyectos(){

        $query = "SELECT * FROM proyectos";
        $proyectos = $this->query($query);
        return $proyectos->fetchAll();
    }


}