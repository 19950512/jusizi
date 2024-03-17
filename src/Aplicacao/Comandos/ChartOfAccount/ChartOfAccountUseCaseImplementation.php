<?php

declare(strict_types=1);

namespace App\Application\Commands\ChartOfAccount;

use App\Dominio\Repositorios\ChartOfAccount\Fronteiras\OutputCOAGetByID;
use Exception;
use App\Application\Commands\Log\Log;
use App\Dominio\ObjetoValor\TextoSimples;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Dominio\ObjetoValor\Description;
use App\Application\Commands\Log\Enumerados\Level;
use App\Dominio\Entidades\ChartOfAccount\ChartOfAccountEntity;
use App\Dominio\Repositorios\ChartOfAccount\Fronteiras\InputNewCOA;
use App\Dominio\Repositorios\ChartOfAccount\Fronteiras\InputUpdateCOA;
use App\Dominio\Repositorios\ChartOfAccount\ChartOfAccountRepository;
use App\Application\Commands\ChartOfAccount\Fronteiras\InputBoundaryUpdateChartOfAccount;
use App\Application\Commands\ChartOfAccount\Fronteiras\InputBoundaryCreateNewChartOfAccount;

class ChartOfAccountUseCaseImplementation implements ChartOfAccountUseCase
{
    public function __construct(
        private readonly IdentificacaoUnica $businessID,
        private ChartOfAccountRepository $_chartOfAccountRepository,
        private Log $_log,
    ){}

    public function createNewChartOfAccount(InputBoundaryCreateNewChartOfAccount $params): void
    {

        $chartOfAccountEntity = new ChartOfAccountEntity(
            id: new IdentificacaoUnica(),
            name: new TextoSimples($params->name),
            description: new Description($params->description)
        );

        if($this->_chartOfAccountRepository->existsCharOfAccountByName($chartOfAccountEntity->name->get())){
            $mensagem = "Já existe um plano de contas com esse nome. ({$chartOfAccountEntity->name->get()})";
            $this->_log->log(
                level: Level::ERROR,
                message: $mensagem,
            );
            throw new Exception($mensagem);
        }

        $paramsCOARepositoryData = new InputNewCOA(
            businessID: $this->businessID->get(),
            id: $chartOfAccountEntity->id->get(),
            name: $chartOfAccountEntity->name->get(),
            description: $chartOfAccountEntity->description->get(),
        );

        try {

            $this->_chartOfAccountRepository->createNewChartOfAccount($paramsCOARepositoryData);

            $this->_log->log(
                level: Level::INFO,
                message: "Plano de conta {$paramsCOARepositoryData->name} criado com sucesso.",
            );

        }catch(Exception $e) {
            $mensagem = "Não foi possível criar o plano de contas. {$paramsCOARepositoryData->name}}";
            $this->_log->log(
                level: Level::CRITICAL,
                message: $mensagem,
            );
            throw new Exception($mensagem);
        }
    }

    public function checkIfCharOfAccountExistsByName(string $name): bool
    {
        return $this->_chartOfAccountRepository->existsCharOfAccountByName($name);
    }

    public function updateChartOfAccount(InputBoundaryUpdateChartOfAccount $params): void
    {

        $chartOfAccountData = $this->_chartOfAccountRepository->getByID($params->id);

        // charOfAccount atual
        $chartOfAccountEntity = new ChartOfAccountEntity(
            id: new IdentificacaoUnica($chartOfAccountData->id),
            name: new TextoSimples($chartOfAccountData->name),
            description: new Description($chartOfAccountData->description)
        );

        // charOfAccount NOVO
        $chartOfAccountEntity->name = new TextoSimples($params->name);
        $chartOfAccountEntity->description = new Description($params->description);

        $paramsCOARepositoryData = new InputUpdateCOA(
            id: $chartOfAccountEntity->id->get(),
            name: $chartOfAccountEntity->name->get(),
            description: $chartOfAccountEntity->description->get(),
        );

        try {

            $this->_chartOfAccountRepository->updateChartOfAccount($paramsCOARepositoryData);

            $this->_log->log(
                level: Level::INFO,
                message: "Plano de conta {$paramsCOARepositoryData->name} atualizado com sucesso.",
            );

        }catch(Exception $e) {
            $mensagem = "Não foi possível atualizar o plano de contas. {$paramsCOARepositoryData->name}}";
            $this->_log->log(
                level: Level::CRITICAL,
                message: $mensagem,
            );
            throw new Exception($mensagem);
        }
    }

    public function getAllChartOfAccounts(): array
    {

        $chartOfAccount = $this->_chartOfAccountRepository->getAllCOA();

        return $chartOfAccount;
    }

    public function getCharOfAccountByName(string $name): OutputCOAGetByID
    {
        $chartOfAccountData = $this->_chartOfAccountRepository->getCharOfAccountByName($name);

        return $chartOfAccountData;
    }
}