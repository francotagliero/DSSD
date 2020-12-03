<?php



class Backend extends TwigView
{

    public function show($arg) {
        
        echo self::getTwig()->render('backend.html.twig', $arg);
        
        
    }
}