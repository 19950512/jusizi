<?php

declare(strict_types=1);

namespace App\Application\Commands\Auth;

use App\Application\Commands\Log\Discord;
use App\Application\Commands\Log\DiscordChannel;
use App\Application\Queries\Business\Fronteiras\InputBoundaryQuerieCreateAccount;
use App\Application\Queries\Business\BusinessQueriesUsecase;
use App\Application\Queries\Business\BusinessQueriesUsecaseImplementation;
use App\Dominio\Entidades\Business\EntidadeEmpresarial;
use Exception;
use Firebase\JWT\JWT;
use App\Shared\Environment;
use App\Dominio\ObjetoValor\Email;
use App\Application\Commands\Log\Log;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Commands\Log\Enumerados\Level;
use App\Application\Commands\Autenticacao\AuthUseCase;
use App\Application\Entities\Autenticacao\AccountEntity;
use App\Dominio\Repositorios\Autenticacao\AuthRepository;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\InputCreateAccount;
use App\Application\Commands\Autenticacao\Fronteiras\InputBoundaryToken;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\InputCreateBusiness;
use App\Application\Commands\Autenticacao\Fronteiras\OutputBoundaryToken;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\OutputGetAccountByID;
use App\Application\Commands\Autenticacao\Fronteiras\InputBoundaryCreateAccount;
use App\Application\Commands\Autenticacao\Fronteiras\InputBoundaryCreateBusiness;
use Firebase\JWT\Key;

class AuthUseCaseImplementation implements AuthUseCase
{
    readonly private BusinessQueriesUsecase $_businessQueriesUsecase;

    public function __construct(
        readonly private AuthRepository $_authRepository,
        readonly private Discord $discord,
        readonly private Environment $_env,
    ){}

    public function token(InputBoundaryToken $params): OutputBoundaryToken
    {

        if(!$this->_authRepository->accountExistsByEmailAndPassword(
            email: $params->email,
            password: $params->password
        )){

            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de login com credenciais inválidas. ({$params->email}, {$params->password})");

            throw new Exception('Credenciais inválidas.');
        }

        $account = $this->_authRepository->getAccountByEmail($params->email);

        $time_exp = strtotime($this->_env->get('JWT_TTL'));

        $appDominio = "https://{$this->_env->get('APP_DOMINIO')}";

        $payload = [
            'iss' => $appDominio,
            'aud' => $appDominio,
            'iat' => time(),
            'nbf' => strtotime('now'),
            'exp' => $time_exp,
            'acc' => $account->id
        ];

        $access_token = JWT::encode($payload, $this->_env->get('JWT_KEY'), $this->_env->get('JWT_ALG'));

        $this->_authRepository->saveJWToken($access_token, $account->id, $account->businessID);

        $returnToken = new OutputBoundaryToken(
            accessToken: $access_token,
            expiresIn: 7200, // 7200 seconds = 2 hours => 60 (seconds) * 60 (minutes) * 2 (Hours)
            tokenType: 'Bearer'
        );

        $this->discord->send(DiscordChannel::get(Level::INFO), "Usuário logado com sucesso. ({$account->nickname})");

        return $returnToken;
    }


    public function createBusiness(InputBoundaryCreateBusiness $params): string
    {

        if(empty($params->name) OR $params->name == ''){
            throw new Exception('Informe um nome da empresa válido.');
        }

        $empresaID = new IdentificacaoUnica();

        if($this->_authRepository->businessExistsByID($empresaID->get())){
            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de cadastro de empresa com ID já existente. ({$empresaID->get()})");
            throw new Exception('Não foi possível criar a empresa, tente novamente.');
        }

        $paramsNewBusiness = new InputCreateBusiness(
            id: $empresaID->get(),
            name: $params->name
        );

        $this->_authRepository->saveNewBusiness($paramsNewBusiness);

        $this->discord->send(DiscordChannel::get(Level::INFO), "Empresa criada com sucesso. ({$paramsNewBusiness->name} - {$paramsNewBusiness->id})");

        return $empresaID->get();
    }

    public function createAccount(InputBoundaryCreateAccount $params): void
    {

        if(empty($params->businessID) OR $params->businessID == ''){
            throw new Exception('Informe um ID da empresa válido.');
        }

        try {

            // lets try get business by ID
           $businessData = $this->_authRepository->getBusinessByID($params->businessID);
           $businessEntity = EntidadeEmpresarial::buildBusinessEntity($businessData);

            $_businessQueriesUsecase = new BusinessQueriesUsecaseImplementation(
                businessID: $businessEntity->code
            );

        }catch(Exception $erro){

            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de cadastro com ID de empresa inválido. ({$params->businessID})");

            throw new Exception('Informe um ID da empresa válido.');
        }


        if(strlen($params->password) < 12){

            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de cadastro com senha muito fraca. ({$params->email}, {$params->password})");
        
            throw new Exception('A senha está muito fraca.');
        }

        $account_ID = new IdentificacaoUnica();

        try {
            $account_email = new Email($params->email);
        }catch(Exception $erro){
            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de cadastro com e-mail inválido. ({$params->email})");
            throw new Exception('Informe um e-mail válido.');
        }

        if($this->_authRepository->accountExistsByEmail($account_email->get())){

            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de cadastro com e-mail já existente. ({$params->email})");

            throw new Exception('Já existe uma conta com esse email, tente outro.');
        }

        try {
            $account_nickname = new NomeCompleto($params->nickname);
        }catch(Exception $erro){

            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de cadastro com nickname inválido. ({$params->nickname})");

            throw new Exception('Informe um nickname válido.');
        }

        $accountEntity = new AccountEntity(
            id: $account_ID,
            nickname: $account_nickname,
            email: $account_email
        );

        $paramsNewAccount = new InputCreateAccount(
            businessID: $params->businessID,
            id: $accountEntity->id->get(),
            nickname: $accountEntity->nickname->get(),
            email: $accountEntity->email->get(),
            password: $params->password
        );

        try {

            $this->_authRepository->createAccount($paramsNewAccount);

            $paramsQueriesNewAccount = new InputBoundaryQuerieCreateAccount(
                business: $businessEntity,
                code: $accountEntity->id->get(),
                name: $accountEntity->nickname->get(),
                email: $accountEntity->email->get(),
                phone: ''
            );
            $_businessQueriesUsecase->createAccount($paramsQueriesNewAccount);

            $this->discord->send(DiscordChannel::get(Level::INFO), "Conta criada com sucesso. ({$paramsNewAccount->email}, {$paramsNewAccount->nickname} - ACC ID: {$paramsNewAccount->id} - BusinessID: {$paramsNewAccount->businessID})");

        }catch(Exception $erro){

            $this->discord->send(DiscordChannel::get(Level::ERROR), "Erro ao criar conta. ({$paramsNewAccount->email}, {$paramsNewAccount->nickname})");

            throw new Exception('Erro ao criar conta.');
        }
    }

    public function getAccountByID(string $id): OutputGetAccountByID
    {
        return $this->_authRepository->getAccountByID($id);
    }


    public function getAuthJTW(string $token, string $acc_id, string $businessID): string
    {
        $tokenDecoded = JWT::decode($token, new Key($this->_env->get('JWT_KEY'), $this->_env->get('JWT_ALG')));

        if(!property_exists($tokenDecoded, 'acc')){

            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de login com token inválido. ({$token}) - X1");
            throw new Exception('Token inválido. - X1');
        }

        $account = $this->_authRepository->getAccountByID($tokenDecoded->acc);

        if($account->id !== $tokenDecoded->acc){

            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de login com token inválido. ({$token}) - X8");

            throw new Exception('Algo de estranho aconteceu com seu acesso. - X8');
        }

        if(empty($account->businessID)){

            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de login com token inválido. ({$token}) - X7");

            throw new Exception('Você não tem uma empresa vinculada a sua conta. - X7');
        }

        if($account->businessID !== $businessID){
            $this->discord->send(DiscordChannel::get(Level::ERROR), "Você não tem uma empresa vinculada a sua conta.. ({$token}) - X42");

            throw new Exception('Você não tem uma empresa vinculada a sua conta. - X42');
        }

        try {

            $tokenSaved = $this->_authRepository->getJWToken($token, $account->id, $account->businessID);
            return $tokenSaved;

        }catch (Exception $erro) {
            $this->discord->send(DiscordChannel::get(Level::ERROR), "Tentativa de login com token inválido. ({$token}) - X77");

            throw new Exception('Token inválido. - X77');
        }
    }
}