<?php

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
session_start();


/* CONTROLLER */
require_once('controller/BaseController.php');
require_once('controller/ProtocoloController.php');
require_once('controller/GuzzleController.php');
require_once('controller/AccessController.php');
require_once('controller/RequestController.php');
require_once('controller/SesionController.php');

/* CLASS */
require_once('class/Usuario.php');
require_once('class/Proyecto.php');

/* MODEL */
require_once('model/PDORepository.php');
require_once('model/UsuarioRepository.php');
require_once('model/ProtocoloRepository.php');
require_once('model/ProyectoRepository.php');

/* VIEW */
require_once('view/TwigView.php');
require_once('view/Login.php');
require_once('view/Protocolo.php');
require_once('view/ConfiguracionProtocolos.php');


if (isset($_GET["action"])){
    switch($_GET['action']){


        /* CLOUD */
        case 'cloud': { BaseController::getInstance()->cloud(); break; }

        /* PROTOCOLO */
        case 'protocolos': { ProtocoloController::getInstance()->getProtocolos(); break; }

        case 'token': { BaseController::getInstance()->conexion(); break; }

        case 'gateway': { BaseController::getInstance()->getProcessId(); break; }

        case 'login': { BaseController::getInstance()->login(); break; }

        case 'logout': { BaseController::getInstance()->logout(); break; }

        case 'instanciacion': { BaseController::getInstance()->instanciacion(); break; }

        case 'configuracion_protocolos': { ProtocoloController::getInstance()->configurarProtocolos(); break; }

        default: { BaseController::getInstance()->home(); break; }
    }
} else {
    BaseController::getInstance()->home();
}




?>