<?php

/*
 *
 *
 *
 */

class Usuario extends PDORepository {

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {

    }

    public function isLogged($state) {
        return ($state == "logged");
    }

    public function isAdmin($rol) {
        return ($rol == "1");
    }

    public function isGestion($rol){
        return ($rol == "2");
    }

    public function isOnline($rol){
        return ($rol == "3");
    }

    public function alta_usuario($usuario) {
        if ($usuario['nombre_trabajo'] != '' AND $usuario['descripcion_trabajo'] != '' AND $usuario['rol'] == 3) {
            //Hago un alta de la hubicacion y me traido el id para guardarlo en el usuario.
            $mapper = function($row) {
                return $row['id'];
            };
            $this->queryList("INSERT INTO ubicacion (nombre, descripcion) VALUES(?, ?)", [$usuario['nombre_trabajo'], $usuario['descripcion_trabajo']], $mapper);
            $ubicacion = $this->queryList("SELECT id FROM ubicacion WHERE nombre=? AND descripcion=?", [$usuario['nombre_trabajo'], $usuario['descripcion_trabajo']], $mapper);
            $usuario['ubicacion_id'] = $ubicacion[0];
        }
        elseif ($usuario['rol'] == 3) {
            //Si no puso ningun trabajo y el rol es Usuario Online
            return Message::getMessage(15);
        }

        if (!$this->userExist($usuario['user'])) {
            $sql = "INSERT INTO usuario (usuario, clave, nombre, apellido, documento, email, telefono, rol_id, ubicacion_id, habilitado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $values = [$usuario['user'], $usuario['password'], $usuario['nombre'], $usuario['apellido'], $usuario['dni'], $usuario['email'], $usuario['telefono'], $usuario['rol'], $usuario['ubicacion_id'], $usuario['acceso']];
            $mapper = function($row){};

            $this->queryList($sql, $values, $mapper);
            return Message::getMessage(3);
        }
        return Message::getMessage(4);
    }

    public function modificar_usuario($usuario){
        $sql = "UPDATE usuario SET clave = ?, nombre = ?, apellido = ?, documento = ?, email = ?, telefono = ?, rol_id = ?, habilitado = ? WHERE id = ?";
        $values = [$usuario['password'], $usuario['nombre'], $usuario['apellido'], $usuario['dni'], $usuario['email'], $usuario['telefono'], $usuario['rol'], $usuario['habilitado'], $usuario['id']];

        if ($usuario['nombre_trabajo'] != '' AND $usuario['descripcion_trabajo'] != '' AND $usuario['rol'] == 3) {
            //Actualizo los datos de la ubicacion.
            $mapper = function($row) {
                return $row['id'];
            };
            if ($usuario['ubicacion_id'] == 0) {
                //Si no tiene una hubicacion la crea
                $this->queryList("INSERT INTO ubicacion (nombre, descripcion) VALUES(?, ?)", [$usuario['nombre_trabajo'], $usuario['descripcion_trabajo']], $mapper);
                $ubicacion = $this->queryList("SELECT id FROM ubicacion WHERE nombre=? AND descripcion=?", [$usuario['nombre_trabajo'], $usuario['descripcion_trabajo']], $mapper);
                $usuario['ubicacion_id'] = $ubicacion[0];

                $sql = "UPDATE usuario SET clave = ?, nombre = ?, apellido = ?, documento = ?, email = ?, telefono = ?, rol_id = ?, ubicacion_id = ?, habilitado = ? WHERE id = ?";
                $values = [$usuario['password'], $usuario['nombre'], $usuario['apellido'], $usuario['dni'], $usuario['email'], $usuario['telefono'], $usuario['rol'], $usuario['ubicacion_id'], $usuario['habilitado'], $usuario['id']];
            }
            else {
                $this->queryList("UPDATE ubicacion SET nombre = ?, descripcion = ? WHERE id = ?", [$usuario['nombre_trabajo'], $usuario['descripcion_trabajo'], $usuario['ubicacion_id']], $mapper);
            }
        }
        elseif ($usuario['rol'] == 3) {
            //Si no puso ningun trabajo y el rol es Usuario Online
            return Message::getMessage(15);
        }

        $mapper=function($row){};
        $this->queryList($sql, $values, $mapper);
        return Message::getMessage(10);
    }

    public function eliminar_usuario($user_id){
        $sql="DELETE FROM usuario WHERE id=?";
        $value=[$user_id];
        $mapper=function($row){};
        $this->queryList($sql,$value,$mapper);
        return Message::getMessage(13);
    }

    public function userExist($user) {
        $mapper = function($row) {
            return $row['usuario'];
        };

        $answer = $this->queryList("SELECT * FROM usuario WHERE usuario=?", [$user], $mapper);

        return (count($answer) > 0);
    }

    public function listar_usuarios($cantElementos, $pagina) {
        $mapper = function($row) {
            return $row;
        };

        $desde = ($pagina - 1) * $cantElementos;

        return $this->queryList("SELECT * FROM usuario LIMIT $desde,$cantElementos", [], $mapper);
    }

    public function datos_usuario($idUser) {
        $mapper = function($row) {
            return $row;
        };
        $usuario = $this->queryList("SELECT * FROM usuario WHERE id=?", [$idUser], $mapper);
        $ubicacion = $this->queryList("SELECT * FROM ubicacion WHERE id=?", [$usuario[0]['ubicacion_id']], $mapper);

        return ['usuario' => $usuario, 'ubicacion' => $ubicacion];
    }

    public function getRolUser($user) {
        $mapper = function($row){
            return $row;
        };
        return $this->queryList("SELECT rol_id FROM usuario WHERE usuario=?", [$user], $mapper);
    }

    public function getId($user){
        $mapper = function($row){
            return $row['id'];
        };
        return $this->queryList("SELECT id FROM usuario WHERE usuario=?", [$user], $mapper);
    }

    public function cant_total_elementos() {
        $mapper = function($row){
            return $row['COUNT(id)'];
        };

        return $this->queryList("SELECT COUNT(id) FROM usuario", [], $mapper);
    }
}