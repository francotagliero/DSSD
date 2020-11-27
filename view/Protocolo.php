<?php



class Protocolo extends TwigView {
    
    public function show($arg) {
        
        echo self::getTwig()->render('protocolos.html.twig', $arg);
        
        
    }

  
    
}