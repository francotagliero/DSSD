<?php

class MonitorController{
    private static $instance;

    private $sesion;

    public static function getInstance()
    {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->sesion = SesionController::getInstance();
    }

    // Retorna true si el usuario esta logeado, false en caso contrario.
    public function isLogged()
    {
        return $this->sesion->getSesion('logged');
    }

    public function getTasks()
    {
        $view = new MonitorView();

        $tasks = RequestController::doTheRequest('GET', 'API/bpm/task?p=0&c=1000');

        $archivedTask = RequestController::doTheRequest('GET', 'API/bpm/archivedTask?p=0&c=1000');

        $countTask = RequestController::doTheRequest('GET', 'API/bpm/task?p=0&c=1000');

        $jefeProyectos = ProyectoRepository::getInstance()->getProyectosUsuarios();

        $responsableProtocolos = ProtocoloRepository::getInstance()->getProtocolosUsuarios();

        $proyectos = ProyectoRepository::getInstance()->getCantProyectos()[0];

        $protocolos = ProtocoloRepository::getInstance()->getCantProtocolos()[0];

        $grillaProtocolos = ProtocoloRepository::getInstance()->getProtocolos();

        $grillaProyectos = ProyectoRepository::getInstance()->getProyectosGrilla();


        $view->show(array(
            'username' => $this->sesion->getSesion('user_bonita'),
            'archivedTask' => $archivedTask['data'],
            'countTask' => $countTask,
            'jefeProyectos' => $jefeProyectos,
            'proyectos' => $proyectos,
            'protocolos' => $protocolos,
            'grillaProtocolos' => $grillaProtocolos,
            'grillaProyectos' => $grillaProyectos,
            'responsableProtocolos' => $responsableProtocolos,
            'rol' => $this->sesion->getSesion('rol'),
            'tasks' => $tasks['data']
        ));
    }
}