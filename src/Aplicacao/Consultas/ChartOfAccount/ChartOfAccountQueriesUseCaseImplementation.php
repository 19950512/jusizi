<?php

declare(strict_types=1);

namespace App\Application\Queries\ChartOfAccount;

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Queries\ChartOfAccount\Fronteiras\OutputChartOfAccount;
use App\Dominio\Repositorios\ChartOfAccount\ChartOfAccountQueriesRepository;
use App\Application\Queries\ChartOfAccount\Fronteiras\OutputBoundaryChartOfAccount;

class ChartOfAccountQueriesUseCaseImplementation implements ChartOfAccountQueriesUseCase
{
    public function __construct(
        readonly private IdentificacaoUnica $businessID,
        private ChartOfAccountQueriesRepository $_chartOfAccountRepository,
    ){}

    public function getChartOfAccounts(): OutputBoundaryChartOfAccount
    {

        $resultChartOfAccountsRepository = $this->_chartOfAccountRepository->getChartOfAccounts();

        $ChartOfAccounts = new OutputBoundaryChartOfAccount();

        if(is_array($resultChartOfAccountsRepository) and count($resultChartOfAccountsRepository) > 0){

            foreach($resultChartOfAccountsRepository as $ChartOfAccount){
                
                $ChartOfAccountToList = new OutputChartOfAccount(
                    id: $ChartOfAccount['codigo'],
                    nome: $ChartOfAccount['nome'],
                );

                $ChartOfAccounts->add($ChartOfAccountToList);
            }
        }

        return $ChartOfAccounts;
    }
}