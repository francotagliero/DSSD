<?php



class ConfiguracionProtocolos extends TwigView
{

    public function show($arg) {
        
        echo self::getTwig()->render('configurar_protocolos.html.twig', $arg);
        
        
    }
}