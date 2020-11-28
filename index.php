<?php

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
//session_start();


/* CONTROLLER */
require_once('controller/BaseController.php');
require_once('controller/ProtocoloController.php');
require_once('controller/ProyectoController.php');
require_once('controller/GuzzleController.php');

/* MODEL */
require_once('model/PDORepository.php');
require_once('model/UsuarioRepository.php');
require_once('model/ProtocoloRepository.php');
require_once('model/ProyectoRepository.php');
require_once('model/LoginSystem.php');
require_once('model/Usuario.php');
require_once('model/Session.php');
require_once('model/Message.php');

/* VIEW */
require_once('view/TwigView.php');
require_once('view/Login.php');
require_once('view/Protocolo.php');
require_once('view/Proyecto.php');

if (isset($_GET["action"])){
    switch($_GET['action']){

        /* LOGIN */
        case 'login': { BaseController::getInstance()->login([]); break; }
        case 'login_system': { BaseController::getInstance()->login_system(); break; }
        case 'logout': { BaseController::getInstance()->logout_system(); break; }

        /* CLOUD */
        case 'cloud': { BaseController::getInstance()->cloud(); break; }

        /* PROTOCOLO */
        case 'protocolos': { ProtocoloController::getInstance()->getProtocolos(); break; }

        /* PROYECTO */
        case 'proyectos': { ProyectoController::getInstance()->getProyectos(); break; }

        case 'token': { BaseController::getInstance()->conexion(); break; }

        default: { BaseController::getInstance()->home(); break; }
    }
} else {
    BaseController::getInstance()->home();
}




?>