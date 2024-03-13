<?php

declare(strict_types=1);

namespace App\Infra\Repositories\Token;

use PDO;
use Exception;
use App\Application\Commands\Log\Log;
use App\Dominio\Repositorios\Token\TokenRepository;
use App\Dominio\Repositorios\Token\Fronteiras\InputBoundaryToken;
use App\Dominio\Repositorios\Token\Fronteiras\OutputBoundaryToken;

class TokenRepositoryImplementation implements TokenRepository
{
    public function __construct(
        readonly private PDO $pdo,
        readonly private Log $log
    ){
    }

    public function getToken(int $codigoContaBancaria, string $bancoNome): OutputBoundaryToken
    {   

        $sql = $this->pdo->prepare("SELECT
                apit_codigo,
                apit_token,
                apit_expires_in,
                apit_token_refresh,
                apit_token_expiration_timestamp,
                apit_token_geracao_timestamp
            FROM api_tokens
            WHERE bcc_codigo = :bcc_codigo AND apit_token_banco = :bancoNome ORDER BY apit_token_expiration_timestamp DESC
        ");
        $sql->bindParam(':bancoNome', $bancoNome);
        $sql->bindParam(':bcc_codigo', $codigoContaBancaria);
        $sql->execute();
        $dataFetch = $sql->fetch(PDO::FETCH_ASSOC);
        

        if(!isset($dataFetch['apit_codigo'])){
            throw new Exception('Token não encontrado.');
        }

        return new OutputBoundaryToken(
            id: (string) $dataFetch['apit_codigo'] ?? '',
            token: $dataFetch['apit_token'] ?? '',
            expireIn: $dataFetch['apit_expires_in'] ?? '',
            tokenRefresh: $dataFetch['apit_token_refresh'] ?? '',
            tokenExpirationTime: $dataFetch['apit_token_expiration_timestamp'] ?? '',
            tokenTimeCreated: $dataFetch['apit_token_geracao_timestamp'] ?? ''
        );
    }

    public function createToken(InputBoundaryToken $params): void
    {

        $sql = $this->pdo->prepare(
            'INSERT INTO api_tokens (
                apit_token,
                apit_token_banco, 
                apit_token_expiration_timestamp,
                apit_expires_in, 
                apit_token_expiration_seconds,
                apit_token_refresh, 
                apit_token_scope, 
                apit_token_type, 
                bcc_codigo
            ) VALUES (
                :apit_token,
                :apit_token_banco, 
                :apit_token_expiration_timestamp,
                :apit_expires_in,
                :apit_token_expiration_seconds,
                :apit_token_refresh, 
                :apit_token_scope, 
                :apit_token_type, 
                :bcc_codigo
            )');
        $sql->bindParam(':apit_token', $params->token);
        $sql->bindParam(':apit_token_banco', $params->bancoNome);
        $sql->bindParam(':apit_token_expiration_timestamp', $params->expirationTime);
        $sql->bindParam(':apit_expires_in', $params->expiresIn);
        $sql->bindParam(':apit_token_expiration_seconds', $params->expiresIn);
        $sql->bindParam(':apit_token_refresh',$params->tokenRefresh);
        $sql->bindParam(':apit_token_scope', $params->tokenScope);
        $sql->bindParam(':apit_token_type', $params->tokenType);
        $sql->bindParam(':bcc_codigo', $params->codigoContaBancaria);
        $sql->execute();
        
    }

    public function updateToken(int $codigoContaBancaria, string $bancoNome, string $token, string $expirationTime): void
    {

        $sql = $this->pdo->prepare(
            'UPDATE 
                api_tokens 
            SET 
                apit_token = :apit_token, 
                apit_token_expiration_time = :apit_token_expiration_time
            WHERE 
                bcc_codigo = :codigoContaBancaria 
                AND apit_token_banco = :bancoNome'
            );
        $sql->bindParam(':apit_token', $token);
        $sql->bindParam(':apit_token_expiration_time', $expirationTime);
        $sql->bindParam(':codigoContaBancaria', $codigoContaBancaria);
        $sql->bindParam(':bancoNome', $bancoNome);
        $sql->execute();
        
        
    }
}