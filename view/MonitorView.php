<?php



class MonitorView extends TwigView {

    public function show($arg) {

        echo self::getTwig()->render('monitor.html.twig', $arg);

    }



}