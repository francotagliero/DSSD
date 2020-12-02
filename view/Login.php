<?php



class Login extends TwigView
{

    public function show($args = [])
    {
        $args = array_merge($args, ['isLogged' => false]);
        echo self::getTwig()->render('iniciar_sesion.html.twig', $args);
    }
}