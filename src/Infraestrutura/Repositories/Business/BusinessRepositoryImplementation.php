<?php

declare(strict_types=1);

namespace App\Infra\Repositories\Business;

use App\Application\Commands\Log\Enumerados\Level;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Dominio\Repositorios\Business\Fronteiras\InputBoundaryCreateNewColaboradorRepo;
use App\Dominio\Repositorios\Business\BusinessRepository;
use Exception;
use PDO;
use PDOException;

class BusinessRepositoryImplementation implements BusinessRepository
{

    public function __construct(
        readonly private PDO $pdo,
    ){}

    public function createNewColaborador(InputBoundaryCreateNewColaboradorRepo $params): void
    {

        if (empty($params->nome) || empty($params->email)) {
            throw new Exception('O nome e o e-mail são obrigatórios.');
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
            $sql->bindValue(':acc_id', $params->code);
            $sql->bindValue(':business_id', $params->businessID);
            $sql->bindValue(':acc_nickname', $params->nome);
            $sql->bindValue(':acc_email', $params->email);
            $sql->execute();

        } catch (PDOException $e) {

            throw new Exception("Ocorreu um erro ao executar a consulta.");
        }
    }
}