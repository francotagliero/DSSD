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
        
        $query = "SELECT 
                    protocolos.id_protocolo as protocolo_id, 
                    protocolos.nombre as protocolo_nombre,
                    protocolos.id_responsable as protocolo_responsable,
                    protocolos.fecha_inicio as protocolo_fecha_inicio, 
                    protocolos.fecha_fin as protocolo_fecha_fin, 
                    protocolos.orden as protocolo_orden, 
                    protocolos.puntaje as protocolo_puntaje,
                    protocolos.id_proyecto as protocolo_id_proyecto, 
                    protocolos.estado as protocolo_estado, 
                    protocolos.es_local as protocolo_es_local, 
                    
                    proyectos.id_proyecto as proyecto_id,
                    proyectos.nombre as proyecto_nombre,
                    proyectos.fecha_inicio as proyecto_fecha_inicio,
                    proyectos.fecha_fin as proyecto_fecha_fin,
                    proyectos.id_responsable as proyecto_id_responsable,
                    proyectos.case_id as proyecto_case_id,
                    proyectos.estado as proyecto_estado,
                    proyectos.orden as proyecto_orden
                FROM protocolos 
                INNER JOIN proyectos ON proyectos.id_proyecto = protocolos.id_proyecto 
                WHERE protocolos.borrado <> 1 AND proyectos.borrado <> 1";
        $protocolos = $this->query($query);
        return $protocolos->fetchAll();
    }

    public function getProtocolosProyecto($id_proyecto){
        $consulta = "SELECT * FROM protocolos WHERE id_proyecto = :id_proyecto and borrado = 0";

        $args = array('id_proyecto' => $id_proyecto);

        $mapper = function($elemento) {
            $protocolo = new Protocolo(
            $elemento['id_protocolo'],
            $elemento['nombre'],
            $elemento['id_responsable'],
            $elemento['fecha_inicio'],
            $elemento['fecha_fin'],
            $elemento['orden'],
            $elemento['es_local'],
            $elemento['puntaje'],
            $elemento['id_proyecto'],
            $elemento['estado'],
            $elemento['borrado']
            );

            return $protocolo;
        };

        $lista = $this->queryList($consulta, $args, $mapper);

        return $lista;
    }

    public function actualizarProtocolo($datos){
        $query = "UPDATE protocolos SET id_responsable = :id_responsable, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, orden = :orden, es_local = :es_local WHERE id_protocolo = :id_protocolo and borrado = 0";

        $args = array('id_responsable' => $datos['id_responsable'], 'fecha_inicio' => $datos['fecha_inicio'], 'fecha_fin' => $datos['fecha_fin'], 'orden' => $datos['orden'], 'es_local' => $datos['es_local'], 'id_protocolo' => $datos['id_protocolo']);

        $this->queryArgs($query, $args);
    }

    public function getProtocolo($id){

        $query = "SELECT * FROM protocolos WHERE id_protocolo = ".$id;
        $protocolos = $this->query($query);
        return $protocolos->fetchAll();
    }

    public function ejecutarProtocolo($id){
        $query = ("UPDATE protocolos SET estado = 'ejecutado' WHERE id_protocolo = ".$id);
        $this->query($query);
        return true;
    }

    public function setPuntaje($id, $puntaje){
        $query = ("UPDATE protocolos SET estado = 'completado', puntaje = :puntaje WHERE id_protocolo = :id_protocolo");
        $args = array('puntaje' => $puntaje, 'id_protocolo' => $id);
        $this->queryArgs($query, $args);
        return true;
    }

    public function getProtocolosDesaprobados($idJefe){
        
        $query = "SELECT  protocolos.nombre, protocolos.id_protocolo, protocolos.id_proyecto, proyectos.nombre AS proyecto, protocolos.puntaje FROM proyectos INNER JOIN protocolos ON proyectos.id_proyecto = protocolos.id_proyecto WHERE proyectos.id_responsable = '$idJefe' AND protocolos.puntaje > 0 AND protocolos.puntaje <= 6 AND protocolos.borrado <> 1 AND proyectos.borrado <> 1 AND protocolos.estado = 'completado'";
        $protocolos = $this->query($query);

        return $protocolos->fetchAll();
    }

    public function getProtocolosDesaprobadosDos($idProyecto){
        $query = "SELECT  protocolos.nombre, protocolos.id_protocolo, protocolos.id_proyecto, proyectos.nombre AS proyecto, protocolos.puntaje FROM proyectos INNER JOIN protocolos ON proyectos.id_proyecto = protocolos.id_proyecto WHERE protocolos.borrado <> 1 AND proyectos.borrado <> 1 AND protocolos.estado = 'desaprobado' AND proyectos.id_proyecto = ".$idProyecto;
        $protocolos = $this->query($query);

        return $protocolos->fetchAll();
    }

    public function reiniciarProtocolo($id){
        $query = "UPDATE protocolos SET estado = :estado, puntaje = :puntaje WHERE id_protocolo = :id_protocolo";
        $args = array('estado' => 'pendiente', 'puntaje' => 0, 'id_protocolo' => $id);
        $this->queryArgs($query, $args);

        //OJO CON LAS ACTIVIDADES!!!

        $query = "UPDATE actividades SET estado = :estado WHERE id_protocolo = :id_protocolo";
        $args = array('estado' => 'config', 'id_protocolo' => $id);
        return $this->queryArgs($query, $args);

    }

    public function reiniciarProtocoloSinActividades($idProtocolo){

        $query = "UPDATE protocolos SET estado = :estado, puntaje = :puntaje WHERE id_protocolo = :id_protocolo";
        $args = array('estado' => 'pendiente', 'puntaje' => 0, 'id_protocolo' => $idProtocolo);
        return $this->queryArgs($query, $args);
    }

    public function terminarProtocolo($id){
        $query = "UPDATE protocolos SET estado = :estado WHERE id_protocolo = :id_protocolo";

        $args = array('estado' => 'terminado', 'id_protocolo' => $id);
    return $this->queryArgs($query, $args);
    }

    //HAY QUE ARREAGLAR!!!
    public function reiniciarProyecto($idProyecto){
        $query = "UPDATE proyectos SET estado = :estado WHERE id_proyecto = :id_proyecto";

        $args = array('estado' => 'configuracion', 'id_proyecto' => $idProyecto);

        return $this->queryArgs($query, $args);

    }

    public function altaProtocolo($nombre, $responsable, $fechaInicio, $fechaFin, $orden, $idProyecto, $esLocal){
        $query = "INSERT INTO protocolos (nombre, id_responsable, fecha_inicio, fecha_fin, orden, puntaje, id_proyecto, estado, es_local, borrado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $args = array($nombre, $responsable, $fechaInicio, $fechaFin, $orden, 0, $idProyecto, 'pendiente', $esLocal, 0);
        $this->queryArgs($query, $args);

        $sql = "SELECT MAX(id_protocolo) AS id FROM protocolos";
        return $this->ultimoId($sql);
    }

    public function altaActividad($nombre, $idProtocolo){
        $query = "INSERT INTO actividades (nombre, id_protocolo) VALUES (?, ?);";
        $args = array($nombre, $idProtocolo);
        return $this->queryArgs($query, $args);

    }

    public function cancelarProtocolos($idProyecto){
        $query = "UPDATE protocolos SET borrado = :borrado WHERE id_proyecto = :id_proyecto";

        $args = array('borrado' => 1, 'id_proyecto' => $idProyecto);
        return $this->queryArgs($query, $args);

    }

    public function getProtocolosResponsable($idResponsable){
        $consulta = "
                SELECT 
                    protocolos.id_protocolo as protocolo_id, 
                    protocolos.nombre as protocolo_nombre,
                    protocolos.id_responsable as protocolo_responsable,
                    protocolos.fecha_inicio as protocolo_fecha_inicio, 
                    protocolos.fecha_fin as protocolo_fecha_fin, 
                    protocolos.orden as protocolo_orden, 
                    protocolos.puntaje as protocolo_puntaje,
                    protocolos.id_proyecto as protocolo_id_proyecto, 
                    protocolos.estado as protocolo_estado, 
                    protocolos.es_local as protocolo_es_local, 
                    
                    proyectos.id_proyecto as proyecto_id,
                    proyectos.nombre as proyecto_nombre,
                    proyectos.fecha_inicio as proyecto_fecha_inicio,
                    proyectos.fecha_fin as proyecto_fecha_fin,
                    proyectos.id_responsable as proyecto_id_responsable,
                    proyectos.case_id as proyecto_case_id,
                    proyectos.estado as proyecto_estado,
                    proyectos.orden as proyecto_orden
                FROM protocolos 
                INNER JOIN proyectos ON proyectos.id_proyecto = protocolos.id_proyecto 
                WHERE protocolos.borrado <> 1 AND proyectos.borrado <> 1 AND proyectos.case_id IS NOT NULL AND protocolos.id_responsable =".$idResponsable;

        //$args = array('idResponsable' => $idResponsable);

        $stm = $this->query($consulta);
        return $stm->fetchAll();

    }

    public function getActividades($idProtocolo){
        $query = "SELECT * FROM actividades WHERE id_protocolo = ".$idProtocolo;
        $actividades = $this->query($query);
        return $actividades->fetchAll();
    }

    public function getIdProtocoloByActividad($idActividad){
        $query = "SELECT id_protocolo FROM actividades WHERE id_actividad = ".$idActividad;
        $actividades = $this->query($query);
        return $actividades->fetch();
    }

    public function cambiarEstadoActividad($idActividad, $estado){
        $query = "UPDATE actividades SET estado = :estado WHERE id_actividad = :id_actividad";

        $args = array('estado' => $estado, 'id_actividad' => $idActividad);
        return $this->queryArgs($query, $args);
    }

    public function cantProtocolosProyecto($idProyecto){
        $query = "SELECT * FROM protocolos WHERE id_proyecto = ".$idProyecto;
        $protocolos = $this->query($query);
        return $protocolos->fetchAll();
    }

    public function cantProtocolosProyectoPendientes($idProyecto){
        $query = "SELECT * FROM protocolos WHERE estado = 'pendiente' AND id_proyecto = ".$idProyecto;
        $protocolos = $this->query($query);
        return $protocolos->fetchAll();
    }

    public function terminarEjecucionLocalProtocolo($idProtocolo){
        $query = ("UPDATE protocolos SET estado = 'completado' WHERE id_protocolo = ".$idProtocolo);
        $this->query($query);
        return true;
    }

    public function getActividadesAprobadas($idProtocolo){
        $query = "SELECT * FROM actividades WHERE estado = 'Aprobado' AND id_protocolo = ".$idProtocolo;
        $actividades = $this->query($query);
        return $actividades->fetchAll();
    }

    public function setPuntajeProtocolo($idProtocolo, $puntaje){
        $query = ("UPDATE protocolos SET puntaje = :puntaje WHERE id_protocolo = :id_protocolo");
        $args = array('puntaje' => $puntaje, 'id_protocolo' => $idProtocolo);
        $this->queryArgs($query, $args);
        return true;
    }

    public function aprobarProtocolo($idProtocolo){
        $query = ("UPDATE protocolos SET estado = 'terminado' WHERE id_protocolo = ".$idProtocolo);
        $this->query($query);
        return true;
    }

    public function setProtocoloRemotoEjecutado($idProtocolo){
        $query = "INSERT INTO protocolos_ejecutados (id_protocolo_ejecutado_relacion) VALUES (?);";
        $args = array($idProtocolo);
        return $this->queryArgs($query, $args);
    }

    public function getUltimoProtocoloRemotoEjecutado(){
        $query = "SELECT protocolos_ejecutados.id_protocolo_ejecutado_relacion FROM protocolos_ejecutados INNER JOIN (SELECT max(id_protocolo_ejecutado) as cantidad, id_protocolo_ejecutado_relacion FROM protocolos_ejecutados) AS hh ON protocolos_ejecutados.id_protocolo_ejecutado = hh.cantidad";
        $protocolo = $this->query($query);
        return $protocolo->fetchAll();
    }

    public function desaprobarProtocolo($idProtocolo){
        $query = "UPDATE protocolos SET estado = :estado WHERE id_protocolo = :id_protocolo";

        $args = array('estado' => 'desaprobado', 'id_protocolo' => $idProtocolo);
        return $this->queryArgs($query, $args);
    }

    public function getProtocolosUsuarios(){
        $query =
            "SELECT
                count(*) AS total,
                SUM(CASE WHEN p.estado = 'terminado' THEN 1 ELSE 0 END) AS cant_completado, 
                SUM(CASE WHEN p.estado = 'notificado' THEN 1 ELSE 0 END) AS cant_notificado, 
                SUM(CASE WHEN p.estado = 'configuracion' THEN 1 ELSE 0 END) AS cant_pendiente, 
                SUM(CASE WHEN p.estado = 'ejecutado' THEN 1 ELSE 0 END) AS cant_ejecutado, 
                SUM(CASE WHEN p.estado = 'tomar_decision' THEN 1 ELSE 0 END) AS tomar_decision, 
                u.username 
            FROM proyectos p 
                LEFT JOIN usuario u on (p.id_responsable = u.id) 
            GROUP BY u.username
            ORDER BY cant_completado DESC";

        $proyectos = $this->query($query);
        return $proyectos->fetchAll();
    }

    public function getCantProtocolos(){
        $query = "SELECT 
                    count(*) AS total,
                    SUM(CASE WHEN p.estado = 'completado' THEN 1 ELSE 0 END) AS cant_completado, 
                    SUM(CASE WHEN p.estado = 'pendiente' THEN 1 ELSE 0 END) AS cant_pendiente, 
                    SUM(CASE WHEN p.estado = 'ejecutado' THEN 1 ELSE 0 END) AS cant_ejecutado 
                FROM protocolos p";

        $proyectos = $this->query($query);
        return $proyectos->fetchAll();
    }

   
}