<?php

declare(strict_types=1);

namespace App\Infraestrutura\Repositorios\Empresa;

use App\Dominio\Repositorios\Empresa\Fronteiras\EntradaFronteiraNovoColaborador;
use App\Dominio\Repositorios\Empresa\RepositorioEmpresa;
use Exception;
use PDO;
use PDOException;

readonly class ImplementacaoRepositorioEmpresa implements RepositorioEmpresa
{

    public function __construct(
        private PDO $pdo,
    ){}

    public function novoColaborador(EntradaFronteiraNovoColaborador $params): void
    {

        if (empty($params->nomeCompleto) || empty($params->email)) {
            throw new Exception('O nome completo e o e-mail são obrigatórios.');
        }

        try {

            $sql = $this->pdo->prepare("INSERT INTO accounts (
                acc_id,
                business_id,
                acc_nickname,
                acc_email
            ) VALUES (
                :acc_id,
                :business_id,
                :acc_nickname,
                :acc_email
            )");
            $sql->bindValue(':acc_id', $params->colaboradorCodigo);
            $sql->bindValue(':business_id', $params->empresaCodigo);
            $sql->bindValue(':acc_nickname', $params->nomeCompleto);
            $sql->bindValue(':acc_email', $params->email);
            $sql->execute();

        } catch (PDOException $e) {

            throw new Exception("Ocorreu um erro ao executar a consulta.");
        }
    }
}