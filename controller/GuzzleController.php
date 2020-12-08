<?php

require_once './vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SessionCookieJar;

class GuzzleController
{
    private static $user = 'walter.bates';
    private static $password = 'bpm';
    private static $base_uri = 'http://localhost:8080/bonita/'; //8080
    private static $cliente = null;
    private static $token = null;

    public static function getGuzzleClient(){
        if(static::$cliente === null){
            //Creo una cookie jar para almacenar las cookies que me va a devolver Bonita luego del request del loginservice
            $cookieJar = new SessionCookieJar('MiCookie', true);
            $gcliente = new Client([
                // Base URI is used with relative requests
                'base_uri' => static::$base_uri,
                // Timeout en segundos
                'timeout'  => 4.0,
                'cookies' => $cookieJar
            ]);

            $resp = $gcliente->request('POST', 'loginservice', [
                'form_params' => [
                    'username' => static::$user,
                    'password' => static::$password,
                    'redirect' => 'false'
                ]
            ]);

            $token = $cookieJar->getCookieByName('X-Bonita-API-Token');
            static::$token = $token->getValue();

            static::$cliente = $gcliente;
        }

        return static::$cliente;
    }

    public static function getToken(){
        return static::$token;
    }

    public static function doTheRequest($method, $uri, $data=null){
        $response = array();

        try {
            $client = static::getGuzzleClient();
            $request = $client->request($method, $uri,
                [
                    'headers' => [
                        'X-Bonita-API-Token' => static::$token
                    ]
                ]);

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $error = Psr7\str($e->getResponse());
            } else {
                $error = "No se puede conectar al servidor de Bonita OS";
            }

            return $error;
        }

        return $request;
    }
}