<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Auth;

use Exception;
use DI\Container;
use App\Application\Commands\Autenticacao\AuthUseCase;
use App\Application\Commands\Autenticacao\Fronteiras\InputBoundaryToken;
use App\Application\Commands\Autenticacao\Fronteiras\InputBoundaryCreateAccount;
use App\Application\Commands\Autenticacao\Fronteiras\InputBoundaryCreateBusiness;

class AuthController
{
    public $method;

    private AuthUseCase $_authUsecase;

    public function __construct(
        private Container $container
    ){

        $this->_authUsecase = $this->container->get(AuthUseCase::class);

        $this->method = $_SERVER['REQUEST_METHOD'] ?? '';

        if(is_array($_POST) and count($_POST) == 0){
            $json = file_get_contents('php://input');
            $_POST = json_decode(json_decode(json_encode($json), true), true);
        }
        /* $eventBus = $this->container->get(EventBus::class);


        $eventBus->publish(
            event: Event::EnviarEmail,
            message: json_encode([
                'nome' => 'Matheus Maydana',
                'email' => 'mattmaydana@gmail.com',
                'body' => '<h1>Hail King!</h1> - HORA - '.date('d/m/Y H:i:s'),
            ]),
        ); */
    }

    public function response(array $data){

        header('Content-Type: application/json; charset=utf-8');
        header('X-Powered-By: Hanabi');

        if(isset($data['statusCode']) and is_numeric($data['statusCode'])){
            header("HTTP/1.0 {$data['statusCode']}");
            unset($data['statusCode']);
        }

        echo json_encode($data['data'] ?? $data);
    }
    public function index()
    {
        return $this->response([
            'message' => 'auth - index'
        ]);
    }

    public function business()
    {

        if($this->method !== 'POST'){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Method não permitido, use POST'
            ]);
        }

        if(!isset($_POST['name']) OR empty($_POST['name'])){
            
            return $this->response([
                'statusCode' => 403,
                'message' => 'Parametros inexistentes ou vazios - name.'
            ]);
        }

        try {

            $paramsNewBusiness = new InputBoundaryCreateBusiness(
                name: $_POST['name'],
            );

            $empresaID = $this->_authUsecase->createBusiness($paramsNewBusiness);

            $paramsNewAccount = new InputBoundaryCreateAccount(
                businessID: $empresaID,
                email: $_POST['email'],
                password: $_POST['password'],
                nickname: $_POST['nickname'] ?? $_POST['name']
            );

            $this->_authUsecase->createAccount($paramsNewAccount);

            return $this->response([
                'statusCode' => 200,
                'message' => 'Empresa criada com sucesso.',
                'id' => $empresaID
            ]);

        }catch(Exception $erro){

            return $this->response([
                'statusCode' => 403,
                'message' => $erro->getMessage()
            ]);
        }
    }


    public function account()
    {

        if($this->method !== 'POST'){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Method não permitido, use POST'
            ]);
        }

        if(!isset($_POST['businessID'], $_POST['nickname'], $_POST['email'], $_POST['password']) OR empty($_POST['businessID']) OR empty($_POST['nickname']) OR empty($_POST['email']) OR empty($_POST['password'])){
            
            return $this->response([
                'statusCode' => 403,
                'message' => 'Parametros inexistentes ou vazios - businessID, Nickname, E-mail ou Password.'
            ]);
        }

        try {

            $paramsNewAccount = new InputBoundaryCreateAccount(
                businessID: $_POST['businessID'],
                email: $_POST['email'],
                password: $_POST['password'],
                nickname: $_POST['nickname']
            );

            $this->_authUsecase->createAccount($paramsNewAccount);

            return $this->response([
                'statusCode' => 200,
                'message' => 'Conta criada com sucesso.'
            ]);

        }catch(Exception $erro){

            return $this->response([
                'statusCode' => 403,
                'message' => $erro->getMessage()
            ]);
        }
    }

    public function token()
    {

        if($this->method !== 'POST'){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Method não permitido, use POST'
            ]);
        }

        if(!isset($_POST['email'], $_POST['password']) OR empty($_POST['email']) OR empty($_POST['password'])){
            
            return $this->response([
                'statusCode' => 403,
                'message' => 'Parametros inexistentes ou vazios - E-mail and Password'
            ]);
        }

        try {

            $paramsToken = new InputBoundaryToken(
                email: $_POST['email'],
                password: $_POST['password']
            );

            $tokenJWT = $this->_authUsecase->token($paramsToken);

            return $this->response([
                'statusCode' => 200,
                'access_token' => $tokenJWT->accessToken,
                'expires_in' => $tokenJWT->expiresIn,
                'token_type' => $tokenJWT->tokenType
            ]);

        }catch(Exception $erro){

            return $this->response([
                'statusCode' => 403,
                'message' => $erro->getMessage()
            ]);
        }
    }
}

