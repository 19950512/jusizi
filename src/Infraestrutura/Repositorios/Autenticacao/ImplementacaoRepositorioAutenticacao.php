<?php

declare(strict_types=1);

namespace App\Infraestrutura\Repositorios\Autenticacao;

use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraEmpresa;
use App\Dominio\Repositorios\Autenticacao\RepositorioAutenticacao;
use PDO;
use Exception;
use PDOException;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Log\Enumerados\Level;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\EntradaFronteiraNovaConta;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\EntradaFronteiraNovaEmpresa;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraBuscarContaPorCodigo;

readonly class ImplementacaoRepositorioAutenticacao implements RepositorioAutenticacao
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
        private PDO $pdo,
        private Log $log
    ){}


    public function obterEmpresaPorCodigo(string $empresaCodigo): SaidaFronteiraEmpresa
    {

        if (empty($empresaCodigo)) {
            $this->log->log(
                level: Level::ERROR,
                message: "O ID da empresa é obrigatório. ($empresaCodigo)"
            );
            throw new Exception('O ID da empresa é obrigatório.');
        }

        try {

            $sql = $this->pdo->prepare("SELECT business_id, business_name FROM businesses WHERE business_id = :business_id");
            $sql->bindValue(':business_id', $empresaCodigo);
            $sql->execute();
            $fetch = $sql->fetch(PDO::FETCH_ASSOC);

            if(!isset($fetch['business_id'])){
                throw new Exception("A empresa $empresaCodigo, não existe na base de dados.");
            }

            return new SaidaFronteiraEmpresa(
                empresaCodigo: $fetch['business_id'] ?? '',
                nome: $fetch['business_name'] ?? '',
                numeroDocumento: ''
            );

        } catch (PDOException $e) {

            $this->log->log(
                level: Level::CRITICAL,
                message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
            );
            throw new Exception(self::MENSAGEM_ERRO_QUERY);
        }
    }

    public function buscarToken(string $token, string $contaCodigo, string $empresaCodigo): string
    {

            if (empty($token) || empty($contaCodigo)) {
                $this->log->log(
                    level: Level::ERROR,
                    message: "O token e o ID da conta são obrigatórios. ($token, $contaCodigo)"
                );
                throw new Exception('O token e o ID da conta são obrigatórios.');
            }

            try {

                $sql = $this->pdo->prepare("SELECT token FROM auth_jwtokens WHERE token = :token AND acc_id = :acc_id AND business_id = :business_id");
                $sql->bindValue(':token', $token);
                $sql->bindValue(':acc_id', $contaCodigo);
                $sql->bindValue(':business_id', $empresaCodigo);
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

    public function novoToken(string $token, string $contaCodigo, string $empresaCodigo): void
    {

            if (empty($token) || empty($contaCodigo)) {
                $this->log->log(
                    level: Level::ERROR,
                    message: "O token e o ID da conta são obrigatórios. ($token, $contaCodigo)"
                );
                throw new Exception('O token e o ID da conta são obrigatórios.');
            }

            try {

                $sql = $this->pdo->prepare("INSERT INTO auth_jwtokens (token, acc_id, business_id) VALUES (:token, :acc_id, :business_id)");
                $sql->bindValue(':business_id', $empresaCodigo);
                $sql->bindValue(':token', $token);
                $sql->bindValue(':acc_id', $contaCodigo);
                $sql->execute();

            } catch (PDOException $e) {

                $this->log->log(
                    level: Level::CRITICAL,
                    message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
                );
                throw new Exception(self::MENSAGEM_ERRO_QUERY);
            }
    }

    public function cadastrarNovaEmpresa(EntradaFronteiraNovaEmpresa $params): void
    {

        if (empty($params->name)) {
            $this->log->log(
                level: Level::ERROR,
                message: "O nome da empresa é obrigatório. ($params->nome)"
            );
            throw new Exception('O nome da empresa é obrigatório.');
        }

        try {

            $sql = $this->pdo->prepare(self::CREATE_BUSINESS);
            $sql->bindValue(':business_id', $params->empresaCodigo);
            $sql->bindValue(':business_name', $params->nome);
            $sql->execute();
            
        } catch (PDOException $e) {
            $this->log->log(
                level: Level::CRITICAL,
                message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
            );
            
            throw new Exception(self::MENSAGEM_ERRO_QUERY);
        }
    }

    public function contaExistePorEmailESenha(string $email, string $senha): bool
    {

        if (empty($email) || empty($senha)) {
            $this->log->log(
                level: Level::ERROR,
                message: "O e-mail e a senha são obrigatórios. ({$email}, {$senha}))"
            );
            throw new Exception('O e-mail e a senha são obrigatórios.');
        }

        try {
            
            $sql = $this->pdo->prepare(self::EXISTS_ACCOUNT_BY_EMAIL_AND_PASSWORD);
            $sql->bindValue(':acc_email', $email);
            $sql->bindValue(':acc_password', $senha);
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

    public function jaExisteContaComEsseEmail(string $email): bool
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

    public function empresaExistePorCodigo(string $empresaCodigo): bool
    {

            if (empty($empresaCodigo)) {
                $this->log->log(
                    level: Level::ERROR,
                    message: "O ID da empresa é obrigatório. ({$empresaCodigo})"
                );
                throw new Exception('O ID da empresa é obrigatório.');
            }

            try {

                $sql = $this->pdo->prepare("SELECT business_id FROM businesses WHERE business_id = :business_id");
                $sql->bindValue(':business_id', $empresaCodigo);
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

    public function buscarContaPorCodigo(string $contaCodigo): SaidaFronteiraBuscarContaPorCodigo
    {
        if (empty($contaCodigo)) {

            $this->log->log(
                level: Level::ERROR,
                message: "O ID é obrigatório. ({$contaCodigo})"
            );
            throw new Exception('O ID é obrigatório.');
        }

        try {
            $account = $this->getAccount('acc_id', $contaCodigo);
            return $account;

        } catch (Exception $e) {

            $this->log->log(
                level: Level::ERROR,
                message: "A conta não existe na base de dados com esse ID {$contaCodigo}."
            );
            throw new Exception("A conta não existe na base de dados com esse ID $contaCodigo.");
        }
    }

    public function buscarContaPorEmail(string $email): SaidaFronteiraBuscarContaPorCodigo
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

    private function getAccount(string $field, string $value): SaidaFronteiraBuscarContaPorCodigo
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

            return new SaidaFronteiraBuscarContaPorCodigo(
                empresaCodigo: $fetch['business_id'],
                contaCodigo: $fetch['acc_id'],
                nomeCompleto: $fetch['acc_nickname'],
                email: $fetch['acc_email']
            );

        } catch (PDOException $e) {

            $this->log->log(
                level: Level::CRITICAL,
                message: "Ocorreu um erro ao executar a consulta ".__FUNCTION__.": {$e->getMessage()}"
            );
            throw new Exception(self::MENSAGEM_ERRO_QUERY);
        }
    }

    public function novaConta(EntradaFronteiraNovaConta $params): void
    {

        if (empty($params->nomeCompleto) || empty($params->email) || empty($params->senha)) {
            $this->log->log(
                level: Level::ERROR,
                message: "O nome completo, o e-mail e a senha são obrigatórios. ({$params->nomeCompleto}, {$params->email}, {$params->senha})"
            );
            throw new Exception('O nome completo, o e-mail e a senha são obrigatórios.');
        }
     
        try {

            $sql = $this->pdo->prepare(self::CREATE_ACCOUNT);
            $sql->bindValue(':acc_id', $params->contaCodigo);
            $sql->bindValue(':business_id', $params->empresaCodigo);
            $sql->bindValue(':acc_nickname', $params->nomeCompleto);
            $sql->bindValue(':acc_email', $params->email);
            $sql->bindValue(':acc_password', $params->senha);
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