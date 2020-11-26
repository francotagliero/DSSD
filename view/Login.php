<?php



class Login extends TwigView {
    
    public function show($arg) {
        
        echo self::getTwig()->render('iniciar_sesion.html.twig', $arg);
        
        
    }

  
    
}