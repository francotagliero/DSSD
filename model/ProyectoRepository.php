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
        
        $query = "UPDATE proyectos SET case_id = :case_id WHERE nombre = :nombre and borrado = 0";

        $args = array('nombre' => $nombre_proyecto, 'case_id' => $id_case);

        $this->queryArgs($query, $args);
    }

    public function getProyectos($id_usuario){
        $consulta = "SELECT * FROM proyectos WHERE id_responsable = :id_responsable and borrado = 0";

        $args = array('id_responsable' => $id_usuario);

        $mapper = function($elemento) {
            $proyecto = new Proyecto(
            $elemento['id_proyecto'],
            $elemento['nombre'],
            $elemento['fecha_inicio'],
            $elemento['fecha_fin'],
            $elemento['id_responsable'],
            $elemento['case_id'],
            $elemento['borrado'],
            $elemento['estado']
            );

            return $proyecto;
        };

        $lista = $this->queryList($consulta, $args, $mapper);

        return $lista;
    }

    public function getIdCase($id){
        $query = "SELECT case_id FROM proyectos WHERE id_proyecto = ".$id;
        $protocolos = $this->query($query);
        return $protocolos->fetchAll();
    }

    public function cancelarProyecto($id){ 
        $query = "UPDATE proyectos SET borrado = :borrado WHERE id_proyecto = :id_proyecto";

        $args = array('borrado' => 1, 'id_proyecto' => $id);
        return $this->queryArgs($query, $args);
    }

    public function altaProyecto($idResponsable, $nombre, $fechaInicio, $fechaFin, $caseId){
        $query = "INSERT INTO proyectos (nombre, fecha_inicio, fecha_fin, id_responsable, case_id, estado, borrado) VALUES (?, ?, ?, ?, ?, ?, ?);";

        $args = array($nombre, $fechaInicio, $fechaFin, $idResponsable, $caseId, 'configuracion', 0);
        return $this->queryArgs($query, $args);
    }

    public function getProyecto($id){
        $query = "SELECT * FROM proyectos WHERE id_proyecto = ".$id;
        $protocolos = $this->query($query);
        return $protocolos->fetchAll()[0];
    }

    public function cambiarEstado($id){
        $query = "UPDATE proyectos SET estado = :estado WHERE id_proyecto = :id_proyecto";

        $args = array('estado' => 'ejecucion', 'id_proyecto' => $id);
        return $this->queryArgs($query, $args);
    }

   
}