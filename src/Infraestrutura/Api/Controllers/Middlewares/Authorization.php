<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Middlewares;

use Exception;
use DI\Container;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Shared\Environment;
use App\Infra\Api\Pages\Autenticacao\AuthPage;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Infra\Api\DynamicPage\DynamicPage;
use App\Infra\Api\DynamicPage\Payload\Align;
use App\Application\Commands\Autenticacao\AuthUseCase;
use App\Application\Commands\Autenticacao\AuthUseCaseImplementation;
use App\Application\Commands\Billing\BillingUseCaseImplementation;
use App\Infra\Api\DynamicPage\Payload\TextType;
use App\Dominio\Entidades\Business\EntidadeEmpresarial;
use App\Dominio\Repositorios\Autenticacao\AuthRepository;
use App\Dominio\Entidades\Business\EntidadeUsuarioLogado;
use App\Dominio\ObjetoValor\Value;
use App\Infra\Api\DynamicPage\Payload\Components\Text;
use App\Infra\Api\DynamicPage\Payload\Components\Space;
use App\Infra\Api\DynamicPage\Payload\Components\TextButton;
use Google\Service\AndroidManagement\Application;

class Authorization
{

    public function __construct(
        private Container $container
    ){

        $headers = apache_request_headers();

        $authorization = explode(' ', $headers['Authorization'] ?? '');
       
        try {

            $env = $this->container->get(Environment::class);
            
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