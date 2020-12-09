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
require_once('controller/ProyectoController.php');

/* CLASS */
require_once('class/Usuario.php');
require_once('class/Proyecto.php');
require_once('class/Actividades.php');
require_once('class/Protocolo.php');

/* MODEL */
require_once('model/PDORepository.php');
require_once('model/UsuarioRepository.php');
require_once('model/ProtocoloRepository.php');
require_once('model/ProyectoRepository.php');

/* VIEW */
require_once('view/TwigView.php');
require_once('view/Login.php');
require_once('view/ProtocoloView.php');
require_once('view/ProyectoView.php');
require_once('view/ConfiguracionProtocolos.php');
require_once('view/Backend.php');
require_once('view/ActividadView.php');


if (isset($_GET["action"])){
    switch($_GET['action']){


        /* CLOUD */
        case 'cloud': { BaseController::getInstance()->cloud(); break; }

        /* PROTOCOLO */
        case 'protocolos': { ProtocoloController::getInstance()->getProtocolos(); break; }

        case 'configurarProtocoloBD': { ProtocoloController::getInstance()->configurarProtocoloBD(); break; }

        case 'configuracion_protocolos': { ProtocoloController::getInstance()->configurarProtocolos(); break; }

        case 'tomar_decision': { ProtocoloController::getInstance()->tomarDecisgetProtocolosion($_GET["p"]); break; }

        case 'reiniciarProtocolo': { ProtocoloController::getInstance()->reiniciarProtocolo($_GET['id']);break;}

        case 'terminarProtocolo': { ProtocoloController::getInstance()->terminarProtocolo($_GET['id']); break; }

        case 'ejecutar': { ProtocoloController::getInstance()->ejecutarProtocolo($_GET["n"]); break; }

        case 'determinar_resultado': { ProtocoloController::getInstance()->determinarResultado($_GET["n"]); break; }

        case 'resolverActividades': { ProtocoloController::getInstance()->resolverActividades($_GET["n"]); break; }

        case 'aprobarActividad': { ProtocoloController::getInstance()->aprobarActividad($_GET["id"]); break; }

        case 'desaprobarActividad': { ProtocoloController::getInstance()->desaprobarActividad($_GET["id"]); break; }

        case 'finalizar_actividades': { ProtocoloController::getInstance()->finalizar_resolver_actividades($_GET["p"]); break; }
        
        //case 'ejecutar_protocolo': { ProtocoloController::getInstance()->ejecutarProtocoloResponsable($_GET["n"]); break; }


        /* PROYECTO */
        case 'proyectos': { ProyectoController::getInstance()->getProyectos(); break;}

        case 'nuevoProyecto': { ProyectoController::getInstance()->nuevoProyecto(); break;}

        case 'nuevoProyectoAction': { ProyectoController::getInstance()->nuevoProyectoAction(); break;}

        case 'reiniciarProyecto': { ProyectoController::getInstance()->reiniciarProyecto($_GET['id']); break; }

        case 'cancelarProyecto': { ProyectoController::getInstance()->cancelarProyecto($_GET['id']); break;}

        case 'agregarProtocolo': { ProyectoController::getInstance()->agregarProtocolo($_GET['p']); break;}

        case 'agregarProtocoloAction': { ProyectoController::getInstance()->agregarProtocoloAction(); break;}

        case 'finalizar_configuracion': { ProyectoController::getInstance()->finalizar_configuracion($_GET['p']); break;}

        /* BACKEND */
        case 'token': { BaseController::getInstance()->conexion(); break; }

        case 'gateway': { BaseController::getInstance()->getProcessId(); break; }

        case 'logout': { BaseController::getInstance()->logout(); break; }

        case 'instanciacion': { BaseController::getInstance()->instanciacion(); break; }

        case 'backend': { BaseController::getInstance()->backend(); break; }

        default: { BaseController::getInstance()->login(); break; }
    }
} else {
    BaseController::getInstance()->home();
}




?>