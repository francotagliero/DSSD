<?php



class DeterminarResultadoView extends TwigView
{

    public function show($arg) {
        
        echo self::getTwig()->render('determinar_resultado.html.twig', $arg);
        
        
    }
}