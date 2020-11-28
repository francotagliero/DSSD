<?php

class Proyecto extends TwigView {

    public function show($arg) {

        echo self::getTwig()->render('proyectos.html.twig', $arg);


    }



}