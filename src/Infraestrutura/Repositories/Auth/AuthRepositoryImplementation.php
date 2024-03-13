<?php

declare(strict_types=1);

namespace App\Infra\Repositories\Auth;

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\OutputBoundaryBusiness;
use PDO;
use Exception;
use PDOException;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Log\Enumerados\Level;
use App\Dominio\Repositorios\Autenticacao\AuthRepository;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\InputCreateAccount;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\InputCreateBusiness;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\OutputGetAccountByID;

class AuthRepositoryImplementation implements AuthRepository
{

    private const MENSAGEM_ERRO_QUERY = 'Ocorreu um erro ao executar a consulta.';

    private const EXISTS_ACCOUNT_BY_EMAIL_AND_PASSWORD = "SELECT acc_id FROM accounts WHERE acc_email = :acc_email AND acc_password = :acc_password";
    private const EXISTS_ACCOUNT_BY_EMAIL = "SELECT acc_id FROM accounts WHERE acc_email = :acc_email";
    private const GET_ACCOUNT = "SELECT 
            acc_id,
            acc_nickname,
            business_id,
            acc_email
        FROM accounts
        WHERE %s = :value";
    
    private const CREATE_ACCOUNT = "INSERT INTO accounts (
            acc_id,
            business_id,
            acc_nickname,
            acc_email,
            acc_password
        ) VALUES (
            :acc_id,
            :business_id,
            :acc_nickname,
            :acc_email,
            :acc_password
        )";
    
    private const CREATE_BUSINESS = "INSERT INTO businesses (
            business_id,
            business_name
        ) VALUES (
            :business_id,
            :business_name
        )";

    public function __construct(
        readonly private PDO $pdo,
        readonly private Log $log
    ){}


    public function getBusinessByID(string $businessID): OutputBoundaryBusiness
    {

        if (empty($businessID)) {
            $this->log->log(
                level: Level::ERROR,
                message: "O ID da empresa é obrigatório. ($businessID)"
            );
            throw new Exception('O ID da empresa é obrigatório.');
        }

        try {

            $sql = $this->pdo->prepare("SELECT business_id, business_name FROM businesses WHERE business_id = :business_id");
            $sql->bindValue(':business_id', $businessID);
            $sql->execute();
            $fetch = $sql->fetch(PDO::FETCH_ASSOC);

            if(!isset($fetch['business_id'])){
                throw new Exception("A empresa $businessID, não existe na base de dados.");
            }

            return new OutputBoundaryBusiness(
                businessID: $fetch['business_id'] ?? '',
                name: $fetch['business_name'] ?? ''
            );

        } catch (PDOException $e) {

            $this->log->log(
                level: Level::CRITICAL,
                message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
            );
            throw new Exception(self::MENSAGEM_ERRO_QUERY);
        }
    }

    public function getJWToken(string $token, string $accountID, string $businessID): string
    {

            if (empty($token) || empty($accountID)) {
                $this->log->log(
                    level: Level::ERROR,
                    message: "O token e o ID da conta são obrigatórios. ($token, $accountID)"
                );
                throw new Exception('O token e o ID da conta são obrigatórios.');
            }

            try {

                $sql = $this->pdo->prepare("SELECT token FROM auth_jwtokens WHERE token = :token AND acc_id = :acc_id AND business_id = :business_id");
                $sql->bindValue(':token', $token);
                $sql->bindValue(':acc_id', $accountID);
                $sql->bindValue(':business_id', $businessID);
                $sql->execute();
                $fetch = $sql->fetch(PDO::FETCH_ASSOC);

                if(!isset($fetch['token'])){
                    throw new Exception("O token $token, não existe na base de dados.");
                }

                return $fetch['token'];

            } catch (PDOException $e) {

                $this->log->log(
                    level: Level::CRITICAL,
                    message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
                );
                throw new Exception(self::MENSAGEM_ERRO_QUERY);
            }
    }

    public function saveJWToken(string $token, string $accountID, string $businessID): void
    {

            if (empty($token) || empty($accountID)) {
                $this->log->log(
                    level: Level::ERROR,
                    message: "O token e o ID da conta são obrigatórios. ($token, $accountID)"
                );
                throw new Exception('O token e o ID da conta são obrigatórios.');
            }

            try {

                $sql = $this->pdo->prepare("INSERT INTO auth_jwtokens (token, acc_id, business_id) VALUES (:token, :acc_id, :business_id)");
                $sql->bindValue(':business_id', $businessID);
                $sql->bindValue(':token', $token);
                $sql->bindValue(':acc_id', $accountID);
                $sql->execute();

            } catch (PDOException $e) {

                $this->log->log(
                    level: Level::CRITICAL,
                    message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
                );
                throw new Exception(self::MENSAGEM_ERRO_QUERY);
            }
    }

    public function saveNewBusiness(InputCreateBusiness $params): void
    {

        if (empty($params->name)) {
            $this->log->log(
                level: Level::ERROR,
                message: "O nome da empresa é obrigatório. ($params->name)"
            );
            throw new Exception('O nome da empresa é obrigatório.');
        }

        try {

            $sql = $this->pdo->prepare(self::CREATE_BUSINESS);
            $sql->bindValue(':business_id', $params->id);
            $sql->bindValue(':business_name', $params->name);
            $sql->execute();
            
        } catch (PDOException $e) {
            $this->log->log(
                level: Level::CRITICAL,
                message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
            );
            
            throw new Exception(self::MENSAGEM_ERRO_QUERY);
        }
    }

    public function accountExistsByEmailAndPassword(string $email, string $password): bool
    {

        if (empty($email) || empty($password)) {
            $this->log->log(
                level: Level::ERROR,
                message: "O e-mail e a senha são obrigatórios. ({$email}, {$password}))"
            );
            throw new Exception('O e-mail e a senha são obrigatórios.');
        }

        try {
            
            $sql = $this->pdo->prepare(self::EXISTS_ACCOUNT_BY_EMAIL_AND_PASSWORD);
            $sql->bindValue(':acc_email', $email);
            $sql->bindValue(':acc_password', $password);
            $sql->execute();
            $fetch = $sql->fetch(PDO::FETCH_ASSOC);

            return isset($fetch['acc_id']) and !empty($fetch['acc_id']);
            
        } catch (PDOException $e) {
            $this->log->log(
                level: Level::CRITICAL,
                message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
            );
            throw new Exception(self::MENSAGEM_ERRO_QUERY);
        }
    }

    public function accountExistsByEmail(string $email): bool
    {

        if (empty($email)) {
            $this->log->log(
                level: Level::ERROR,
                message: "O e-mail é obrigatório. ({$email})"
            );
            throw new Exception('O e-mail é obrigatório.');
        }

        try {
            
            $sql = $this->pdo->prepare(self::EXISTS_ACCOUNT_BY_EMAIL);
            $sql->bindValue(':acc_email', $email);
            $sql->execute();
            $fetch = $sql->fetch(PDO::FETCH_ASSOC);

            return isset($fetch['acc_id']) and !empty($fetch['acc_id']);

        } catch (PDOException $e) {

            $this->log->log(
                level: Level::CRITICAL,
                message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
            );
            throw new Exception(self::MENSAGEM_ERRO_QUERY);
        }
    }

    public function businessExistsByID(string $businessID): bool
    {

            if (empty($businessID)) {
                $this->log->log(
                    level: Level::ERROR,
                    message: "O ID da empresa é obrigatório. ({$businessID})"
                );
                throw new Exception('O ID da empresa é obrigatório.');
            }

            try {

                $sql = $this->pdo->prepare("SELECT business_id FROM businesses WHERE business_id = :business_id");
                $sql->bindValue(':business_id', $businessID);
                $sql->execute();
                $fetch = $sql->fetch(PDO::FETCH_ASSOC);

                return isset($fetch['business_id']) and !empty($fetch['business_id']);

            } catch (PDOException $e) {

                $this->log->log(
                    level: Level::CRITICAL,
                    message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
                );
                throw new Exception(self::MENSAGEM_ERRO_QUERY);
            }
    }

    public function getAccountByID(string $id): OutputGetAccountByID
    {
        if (empty($id)) {

            $this->log->log(
                level: Level::ERROR,
                message: "O ID é obrigatório. ({$id})"
            );
            throw new Exception('O ID é obrigatório.');
        }

        try {
            $account = $this->getAccount('acc_id', $id);
            return $account;

        } catch (Exception $e) {

            $this->log->log(
                level: Level::ERROR,
                message: "A conta não existe na base de dados com esse ID {$id}."
            );
            throw new Exception("A conta não existe na base de dados com esse ID $id.");
        }
    }

    public function getAccountByEmail(string $email): OutputGetAccountByID
    {
        try {
            $account = $this->getAccount('acc_email', $email);
            return $account;

        } catch (Exception $e) {

            $this->log->log(
                level: Level::ERROR,
                message: "A conta não existe na base de dados com esse e-mail {$email}."
            );

            throw new Exception("A conta não existe na base de dados com esse e-mail $email.");
        }
    }

    private function getAccount(string $field, string $value): OutputGetAccountByID
    {
        $allowedFields = ['acc_id', 'acc_email'];
        if (!in_array($field, $allowedFields)) {
            throw new Exception('Campo inválido.');
        }

        try {

            $sql = $this->pdo->prepare(sprintf(self::GET_ACCOUNT, $field));
            $sql->bindValue(':value', $value);
            $sql->execute();
            $fetch = $sql->fetch(PDO::FETCH_ASSOC);

            if(!isset($fetch['acc_id'])){
                throw new Exception("A conta $value, não existe na base de dados.");
            }

            return new OutputGetAccountByID(
                id: $fetch['acc_id'],
                nickname: $fetch['acc_nickname'],
                email: $fetch['acc_email'],
                businessID: $fetch['business_id']
            );

        } catch (PDOException $e) {

            $this->log->log(
                level: Level::CRITICAL,
                message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
            );
            throw new Exception(self::MENSAGEM_ERRO_QUERY);
        }
    }

    public function createAccount(InputCreateAccount $params): void
    {

        if (empty($params->nickname) || empty($params->email) || empty($params->password)) {
            $this->log->log(
                level: Level::ERROR,
                message: "O nickname, o e-mail e a senha são obrigatórios. ({$params->nickname}, {$params->email}, {$params->password})"
            );
            throw new Exception('O nickname, o e-mail e a senha são obrigatórios.');
        }
     
        try {

            $sql = $this->pdo->prepare(self::CREATE_ACCOUNT);
            $sql->bindValue(':acc_id', $params->id);
            $sql->bindValue(':business_id', $params->businessID);
            $sql->bindValue(':acc_nickname', $params->nickname);
            $sql->bindValue(':acc_email', $params->email);
            $sql->bindValue(':acc_password', $params->password);
            $sql->execute();
            

        } catch (PDOException $e) {

            $this->log->log(
                level: Level::CRITICAL,
                message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
            );
            throw new Exception(self::MENSAGEM_ERRO_QUERY);
        }
    }
}