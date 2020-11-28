<?php

/*
**  Descripcion de Message
**
*/

class Message {

    public static function getMessage($message) {
        switch ($message) {
            case 0:
                return ['message' => 'Ups! No tiene permiso para acceder a ese lugar.'];
            case 1:
                return ['message' => 'El usuario o la contraseña son incorrectas.'];
            default:
                return ['message' => 'Ups! No tiene permiso para acceder a ese lugar.'];
        }
    }

}