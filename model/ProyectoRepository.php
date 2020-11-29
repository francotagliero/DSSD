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

        $args = array('nombre' => $nombre_proyecto, 'case_id' => $id_case);

        $this->queryArgs($query, $args);
    }

    public function getIdProyecto($id_usuario){
        $consulta = "SELECT * FROM proyectos WHERE id_responsable = :id_responsable";

        $args = array('id_responsable' => $id_usuario);

        $mapper = function($elemento) {
            $proyecto = new Proyecto(
            $elemento['id_proyecto'],
            $elemento['nombre'],
            $elemento['fecha_inicio'],
            $elemento['fecha_fin'],
            $elemento['id_responsable'],
            $elemento['case_id']
            );

            return $proyecto;
        };

        $lista = $this->queryList($consulta, $args, $mapper);

        return $lista;
    }

   
}