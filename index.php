<?php

//ini_set('display_startup_errors',1);
//ini_set('display_errors',1);
//error_reporting(-1);
//session_start();
require_once('controller/BaseController.php');
require_once('controller/ProtocoloController.php');
require_once('controller/GuzzleController.php');

require_once('model/PDORepository.php');
require_once('model/UsuarioRepository.php');
require_once('model/ProtocoloRepository.php');

require_once('view/TwigView.php');
require_once('view/Login.php');
require_once('view/Protocolo.php');


if (isset($_GET['action']) && $_GET['action'] != 'home'){
    if($_GET["action"] == 'token'){
        BaseController::getInstance()->conexion();
    }
    elseif ($_GET["action"] == 'cloud') {
        BaseController::getInstance()->cloud();
    }
    elseif ($_GET["action"] == 'protocolos') {
        ProtocoloController::getInstance()->getProtocolos();
    }
}else{
    BaseController::getInstance()->home();
}




?>