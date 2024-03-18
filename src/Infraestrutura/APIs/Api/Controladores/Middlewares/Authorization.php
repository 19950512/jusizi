<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Api\Controladores\Middlewares;

use App\Aplicacao\Compartilhado\Ambiente\Ambiente;
use DI\Container;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authorization
{

    public function __construct(
        private Container $container
    ){

        $headers = apache_request_headers();

        $authorization = explode(' ', $headers['Authorization'] ?? '');
       
        try {

            $env = $this->container->get(Ambiente::class);
            
            $token = $authorization[1] ?? '';

            $tokenDecoded = JWT::decode($token, new Key($env->get('JWT_KEY'), $env->get('JWT_ALG')));

            if(!property_exists($tokenDecoded, 'acc')){
                $this->response(['message' => 'Token inválido', 'statusCode' => 401]);
            }
            $authUsecase = $this->container->get(AuthUseCase::class);
            
            try {

                $account = $authUsecase->getAccountByID($tokenDecoded->acc);

                if($account->id !== $tokenDecoded->acc){
                    $this->response(['message' => 'Token inválido', 'statusCode' => 401]);
                }

                if(empty($account->businessID)){
                    $this->response(['message' => 'Token inválido!', 'statusCode' => 401]);
                }

                $authUsecase->getAuthJTW($token, $account->id, $account->businessID);
                $userLogged = EntidadeUsuarioLogado::buildUserLoggedEntity($account);

                try {

                    $repository = $this->container->get(AuthRepository::class);
                    $businessData = $repository->getBusinessByID($account->businessID);

                    $businessEntity = EntidadeEmpresarial::buildBusinessEntity($businessData);

                }catch (Exception $erro) {
                    $this->response(['message' => 'Empresa não encontrada', 'statusCode' => 401]);
                }

                $this->container->set('businessID', new IdentificacaoUnica($account->businessID));
                $this->container->set(EntidadeUsuarioLogado::class, $userLogged);
                $this->container->set(EntidadeEmpresarial::class, $businessEntity);

            }catch(Exception $erro){
                $this->response(['message' => 'Token inválido!!', 'statusCode' => 401]);
            }

        }catch(Exception $erro){
            $this->response(['message' => 'Token inválido!!!', 'statusCode' => 401]);
        }
    }

    public function response(array $data){

        header('Content-Type: application/json; charset=utf-8');
        header('X-Powered-By: Jus IZI');

        if(isset($data['statusCode']) and is_numeric($data['statusCode'])){
            header("HTTP/1.0 {$data['statusCode']}");
            unset($data['statusCode']);
        }

        echo json_encode($data);
    }
}