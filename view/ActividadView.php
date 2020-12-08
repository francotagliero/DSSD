<?php



class ActividadView extends TwigView
{

    public function show($arg) {
        
        echo self::getTwig()->render('actividades.html.twig', $arg);
        
        
    }
}