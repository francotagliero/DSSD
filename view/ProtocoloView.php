<?php



class ProtocoloView extends TwigView {
    
    public function show($arg) {
        
        echo self::getTwig()->render('protocolos.html.twig', $arg);
        
        
    }

    public function tomarDecision($arg) {
        
        echo self::getTwig()->render('tomar_decision.html.twig', $arg);
        
        
    }

    public function mensaje($arg) {
        echo self::getTwig()->render('mensaje.html.twig', $arg);
    }

  
    
}