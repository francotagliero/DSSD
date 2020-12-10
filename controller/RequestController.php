<?php

//session_start();
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Cookie\CookieJar;

class RequestController {

    public static function doTheRequest($method, $uri, $variable=null, $data=null){
        $response = array();
        $client = AccessController::getGuzzleClient();
        try {
            //Si el método es POST, hago el request con un header con la variable de sesion			
			if ($method == 'POST'){
                $jar = new \GuzzleHttp\Cookie\CookieJar();
                $request = $client->request($method, $uri,
                    ['headers' => [
                        'X-Bonita-API-Token' => $_SESSION['X-Bonita-API-Token']
                        ]
                    ]);
                $tareas = $request->getBody();
                $response['success'] = true;
                $response['data'] = json_decode($tareas);

            }
			else{
				if ($method == 'PUT'){
					$jar = new \GuzzleHttp\Cookie\CookieJar();
					$request = $client->request($method, $uri,
						['headers' => [
							'X-Bonita-API-Token' => GuzzleController::getToken()//$_SESSION['X-Bonita-API-Token']
							],
						 'json' => [
							'type' => 'java.lang.Integer',
							'value'=> $data
							]							
						]);
					$tareas = $request->getBody();
					$response['success'] = true;
					$response['data'] = json_decode($tareas);
				}
				else {
					$request = $client->request($method, $uri);
					$tareas = $request->getBody();
					$response['success'] = true;
					$response['data'] = json_decode($tareas);
				}
			}
            //Si el metodo es GET, lo hago directamente.
            


        } catch (RequestException $e) {
            $response['success'] = false;
            $response['message'] = $e->getResponse()->getStatusCode() . ' - ' . $e->getResponse()->getReasonPhrase();
            $response['data'] = [];
        }
        return $response;
    }

    public static function getUserId($client){
        $request = $client->request('GET', '/bonita/API/identity/user??p=0&c=10&o=lastname%20ASC&s=bates&f=enabled%3dtrue',
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],

            ]);
        $body = $request->getBody();
        $json = json_decode($body);

        $response['success'] = true;
        $response['data'] = json_decode($body);

        $idUser = $response['data'][0]->id; #Obtengo el id del usuario

        return $idUser;
    }

    public static function getUserIdDos($client, $username){

        $request = $client->request('GET', 'API/identity/user??p=0&c=10&o=userName%20ASC&s='.$username.'&f=enabled%3dtrue',
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],

            ]);
        $body = $request->getBody();
        $json = json_decode($body);

        $response['success'] = true;
        $response['data'] = json_decode($body);

        $idUser = $response['data'][0]->id; #Obtengo el id del usuario

        return $idUser;

    }

    public static function instanciarProceso($client, $idProceso){
        $request = $client->request('POST', 'API/bpm/process/'.$idProceso.'/instantiation',
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],

            ]);
        $body = $request->getBody();
        $response['data'] = json_decode($body);
        $caseId = $response['data']->caseId; #Obtengo el id de la instancia

        return $caseId;
    }

    public static function asignarTarea($client, $idTask, $idUser){
        $request = $client->request('PUT', '/bonita/API/bpm/userTask/'.$idTask,
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],
                'json' => [
                    "assigned_id" => $idUser,

                ]
            ]);

        $body = $request->getBody();
        $response['data'] = json_decode($body);
        return $response;
    }

    public static function obtenerTarea($client, $caseId){
        $request = $client->request('GET', '/bonita/API/bpm/task?f=caseId='.$caseId,
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],

            ]);

        $body = $request->getBody();
        $response['data'] = json_decode($body);

        return ($response['data'][0]->id);
    }

    public static function ejecutarTarea($client, $idTask){
        $request = $client->request('POST', '/bonita/API/bpm/userTask/'.$idTask.'/execution',
            [
                'headers' => [
                    'X-Bonita-API-Token' => GuzzleController::getToken()
                ],

            ]);

        $body = $request->getBody();
        $response['data'] = json_decode($body);
        return $response;
    }



}