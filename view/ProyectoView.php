<?php

class ProyectoView extends TwigView
{

    public function show($arg)
    {

        echo self::getTwig()->render('proyectos.html.twig', $arg);

    }

    public function nuevo($arg)
    {

        echo self::getTwig()->render('nuevoProyecto.html.twig', $arg);

    }
}