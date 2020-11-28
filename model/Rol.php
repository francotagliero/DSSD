<?php

/*
 *
 *
 *
 */

class Rol extends PDORepository {

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {

    }


    public function getRol($idRol){
        $mapper = function($row){
            return $row;
        };
        return $this->queryList("SELECT * FROM rol WHERE id=?", [$idRol], $mapper);
    }

    public function listar_roles() {
        $mapper = function($row) {
            return $row;
        };
        return $this->queryList("SELECT * FROM rol", [], $mapper);
    }
}