<?php

declare(strict_types=1);

namespace App\Application\Commands\Financial;

use App\Application\Commands\Financial\Fronteiras\InputBoundaryPost;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Queries\Financial\FinancialQuery;
use App\Dominio\Entidades\ChartOfAccount\ChartOfAccountEntity;
use App\Dominio\Entidades\Client\ClientEntity;
use App\Dominio\Repositorios\ChartOfAccount\ChartOfAccountRepository;
use App\Dominio\Repositorios\Client\ClientRepository;
use App\Dominio\Repositorios\Financial\Fronteiras\InputBoundaryPostRepository;
use App\Dominio\Repositorios\Financial\FinancialRepositoryCommand;
use App\Dominio\ObjetoValor\Description;
use App\Dominio\ObjetoValor\Value;

class FinancialUsecaseImplementation implements FinancialUsecase
{
    public function __construct(
        readonly private IdentificacaoUnica                       $businessID,
        readonly private ClientRepository           $clientRepository,
        readonly private FinancialQuery             $financialQuery,
        readonly private FinancialRepositoryCommand $financialRepository,
        readonly private ChartOfAccountRepository   $chartOfAccountRepository
    ){}

    public function post(InputBoundaryPost $params): void
    {

        $value = new Value($params->value);
        $description = new Description($params->description);
        $code = new IdentificacaoUnica();

        $clientData = $this->clientRepository->getClientByID($params->clientID);
        $clientEntity = ClientEntity::buildClientEntity($clientData);

        $charOfAccountData = $this->chartOfAccountRepository->getByID($params->charofaccountID);
        $charOfAccountEntity = ChartOfAccountEntity::buildChartOfAccountEntity($charOfAccountData);

        $saldoAnteriorData = $this->financialRepository->getSaldoAnteriorByClientID($clientEntity->id->get());

        $saldoAnterior = new Value($saldoAnteriorData);

        $saldoAtual = $saldoAnterior->get() + $value->get();

        $saldoAnteriorParam = $saldoAnterior->get();

        $paramsRepository = new InputBoundaryPostRepository(
            code: $code->get(),
            clientID: $clientEntity->id->get(),
            charofaccountID: $charOfAccountEntity->id->get(),
            value: $value->get(),
            saldoAnterior: $saldoAnteriorParam,
            saldoAtual: $saldoAtual,
            description: $description->get()
        );

        $this->financialRepository->post($paramsRepository);

        $this->financialQuery->post($paramsRepository);

        $this->financialQuery->saveSaldoAtual($clientEntity->id->get(), $saldoAtual);
    }
}
