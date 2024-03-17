<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Client;

use Exception;
use DI\Container;
use App\Application\Commands\Client\ClientUseCase;
use App\Application\Queries\Client\ClientQueriesUseCase;
use App\Application\Commands\Client\Fronteiras\InputNewClient;
use App\Application\Commands\Client\Fronteiras\InputUpdateClient;
use App\Infra\Api\Controllers\Middlewares\Controller;

class ClientController extends Controller
{

    private ClientUseCase $_clientUsecase;
    private ClientQueriesUseCase $_clientQueriesUseCase;

    public function __construct(
        private Container $container
    ){

        parent::__construct(
            container: $this->container
        );

        $this->_clientUsecase = $this->container->get(ClientUseCase::class);
        $this->_clientQueriesUseCase = $this->container->get(ClientQueriesUseCase::class);
    }

    public function index()
    {

        if($this->method !== 'POST' and $this->method !== 'GET' and $this->method !== 'DELETE'){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Method não permitido, use POST, GET ou DELETE'
            ]);
        }
        if($this->method == 'DELETE'){

            if(!isset($_GET['id']) OR empty($_GET['id'])){

                return $this->response([
                    'statusCode' => 422,
                    'message' => 'Parametros inexistentes ou vazios - ID'
                ]);
            }

            try {

                $this->_clientUsecase->deleteClient($_GET['id']);

                return $this->response([
                    'statusCode' => 200,
                    'message' => 'Cliente deletado com sucesso.'
                ]);
            } catch (Exception $e) {
                return $this->response([
                    'statusCode' => 500,
                    'message' => $e->getMessage()
                ]);
            }
        }

        if($this->method == 'POST'){

            if(!isset($_POST['phone'], $_POST['name'], $_POST['email'], $_POST['document']) OR empty($_POST['phone']) OR empty($_POST['name']) OR empty($_POST['email']) OR empty($_POST['document'])){
                
                return $this->response([
                    'statusCode' => 422,
                    'message' => 'Parametros inexistentes ou vazios - Name, Phone, Email ou Document'
                ]);
            }

            try {

                if(isset($_POST['id'])){

                    $paramsToken = new InputUpdateClient(
                        id: $_POST['id'],
                        name: $_POST['name'],
                        email: $_POST['email'],
                        phone: $_POST['phone'],
                        document: $_POST['document'],
                    );

                    $this->_clientUsecase->updateClient($paramsToken);

                    return $this->response([
                        'statusCode' => 200,
                        'message' => 'Cliente atualizado com sucesso.'
                    ]);
                }else{

                    $paramsToken = new InputNewClient(
                        name: $_POST['name'],
                        email: $_POST['email'],
                        document: $_POST['document'],
                        phone: $_POST['phone'],
                    );

                    $this->_clientUsecase->newClient($paramsToken);

                    return $this->response([
                        'statusCode' => 200,
                        'message' => 'Cliente cadastrado com sucesso.'
                    ]);
                }

            }catch(Exception $erro){

                return $this->response([
                    'statusCode' => 403,
                    'message' => $erro->getMessage()
                ]);
            }
        }

        if($this->method == 'GET'){

            try {

                $clients = $this->_clientQueriesUseCase->getClients();

                return $this->response([
                    'statusCode' => 200,
                    'data' => $clients->get()
                ]);

            }catch(Exception $erro){

                return $this->response([
                    'statusCode' => 403,
                    'message' => $erro->getMessage()
                ]);
            }
        }
    }
}

