<?php



class Login extends TwigView
{

    public function show($args)
    {
        echo self::getTwig()->render('iniciar_sesion.html.twig', $args);
    }
}