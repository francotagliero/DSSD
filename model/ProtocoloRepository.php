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
        $consulta = "SELECT * FROM protocolos WHERE id_proyecto = :id_proyecto";

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
            $elemento['estado']
            );

            return $protocolo;
        };

        $lista = $this->queryList($consulta, $args, $mapper);

        return $lista;
    }



   
}