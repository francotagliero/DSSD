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
            $elemento['estado'],
            $elemento['orden']
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
        $query = "UPDATE proyectos SET estado = :estado WHERE id_proyecto = :id_proyecto";

        $args = array('estado' => 'cancelado', 'id_proyecto' => $id);
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

    public function actualizarOrden($id, $orden){
        $query = "SELECT * FROM protocolos WHERE estado='pendiente' AND id_proyecto = ".$id." AND orden = ".$orden;
        $protocolos = $this->query($query);
        return $protocolos->fetchAll();

    }

    public function cambiarOrden($idProyecto, $orden){
        $query = "UPDATE proyectos SET orden = :orden WHERE id_proyecto = :id_proyecto";

        $args = array('orden' => $orden, 'id_proyecto' => $idProyecto);
        return $this->queryArgs($query, $args);
    }
    
    public function cambiarEstadoConfiguracion($idProyecto){
        $query = "UPDATE proyectos SET estado = :estado WHERE id_proyecto = :id_proyecto";

        $args = array('estado' => 'configuracion', 'id_proyecto' => $idProyecto);
        return $this->queryArgs($query, $args);
    }

    public function cambiarEstadoTerminado($idProyecto){
        $query = "UPDATE proyectos SET estado = :estado WHERE id_proyecto = :id_proyecto";

        $args = array('estado' => 'terminado', 'id_proyecto' => $idProyecto);
        return $this->queryArgs($query, $args);
    }

    public function cambiarEstadoNotificado($idProyecto){
        $query = "UPDATE proyectos SET estado = :estado WHERE id_proyecto = :id_proyecto";

        $args = array('estado' => 'notificado', 'id_proyecto' => $idProyecto);
        return $this->queryArgs($query, $args);
    }

    public function cambiarEstadoTomarDecision($idProyecto){
        $query = "UPDATE proyectos SET estado = :estado WHERE id_proyecto = :id_proyecto";

        $args = array('estado' => 'tomar_decision', 'id_proyecto' => $idProyecto);
        return $this->queryArgs($query, $args);
    }

    public function getProyectosUsuarios(){
        $query = "SELECT
                count(*) AS total,
                SUM(CASE WHEN p.estado = 'terminado' THEN 1 ELSE 0 END) AS cant_completado, 
                SUM(CASE WHEN p.estado = 'notificado' THEN 1 ELSE 0 END) AS cant_notificado, 
                SUM(CASE WHEN p.estado = 'configuracion' THEN 1 ELSE 0 END) AS cant_pendiente, 
                SUM(CASE WHEN p.estado = 'ejecutado' THEN 1 ELSE 0 END) AS cant_ejecutado, 
                SUM(CASE WHEN p.estado = 'tomar_decision' THEN 1 ELSE 0 END) AS tomar_decision,
                SUM(CASE WHEN p.estado = 'cancelado' THEN 1 ELSE 0 END) AS cant_cancelado,
                u.username 
            FROM proyectos p 
                LEFT JOIN usuario u on (p.id_responsable = u.id) 
            GROUP BY u.username
            ORDER BY cant_completado DESC";

        $proyectos = $this->query($query);
        return $proyectos->fetchAll();
    }

    public function getCantProyectos(){
        $query = "SELECT 
                    count(*) AS total,
                    SUM(CASE WHEN p.estado = 'terminado' THEN 1 ELSE 0 END) AS cant_completado, 
                    SUM(CASE WHEN p.estado = 'notificado' THEN 1 ELSE 0 END) AS cant_notificado, 
                    SUM(CASE WHEN p.estado = 'configuracion' THEN 1 ELSE 0 END) AS cant_pendiente, 
                    SUM(CASE WHEN p.estado = 'ejecutado' THEN 1 ELSE 0 END) AS cant_ejecutado, 
                    SUM(CASE WHEN p.estado = 'tomar_decision' THEN 1 ELSE 0 END) AS tomar_decision,
                    SUM(CASE WHEN p.estado = 'cancelado' THEN 1 ELSE 0 END) AS cant_cancelado 
                FROM proyectos p";

        $proyectos = $this->query($query);
        return $proyectos->fetchAll();
    }

    public function getProyectosGrilla(){
        $query = "SELECT * FROM proyectos";

        $proyectos = $this->query($query);
        return $proyectos->fetchAll();

    }
   
}