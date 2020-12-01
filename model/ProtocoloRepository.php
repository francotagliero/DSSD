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
        $query = ("UPDATE protocolos SET estado = 'finalizado', puntaje = :puntaje WHERE id_protocolo = :id_protocolo");
        $args = array('puntaje' => $puntaje, 'id_protocolo' => $id);
        $this->queryArgs($query, $args);
        return true;
    }

    public function getProtocolosDesaprobados($idJefe){
        
        $query = "SELECT  protocolos.nombre, protocolos.id_protocolo, protocolos.id_proyecto, proyectos.nombre AS proyecto, protocolos.puntaje FROM proyectos INNER JOIN protocolos ON proyectos.id_proyecto = protocolos.id_proyecto WHERE proyectos.id_responsable = '$idJefe' AND protocolos.puntaje > 0 AND protocolos.puntaje <= 6 AND protocolos.borrado <> 1 AND proyectos.borrado <> 1 AND protocolos.estado = 'completado'";
        $protocolos = $this->query($query);

        return $protocolos->fetchAll();
        }

        public function reiniciarProtocolo($id){
            $query = "UPDATE protocolos SET estado = :estado, puntaje = :puntaje WHERE id_protocolo = :id_protocolo";
     
             $args = array('estado' => 'pendiente', 'puntaje' => 0, 'id_protocolo' => $id);
              return $this->queryArgs($query, $args);
             
         }
     
         public function terminarProtocolo($id){
             $query = "UPDATE protocolos SET estado = :estado WHERE id_protocolo = :id_protocolo";
     
             $args = array('estado' => 'terminado', 'id_protocolo' => $id);
             return $this->queryArgs($query, $args);
         }
     
         public function reiniciarProyecto($idProyecto){
            $query = "UPDATE protocolos SET estado = :estado, puntaje = :puntaje WHERE id_proyecto = :id_proyecto";
     
             $args = array('estado' => 'pendiente', 'puntaje' => 0, 'id_proyecto' => $idProyecto);
             return $this->queryArgs($query, $args);
         
         }


   
}