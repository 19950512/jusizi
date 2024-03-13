<?php

declare(strict_types=1);

namespace App\Application\Queries\Contract;

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Queries\Contract\Fronteiras\OutputContract;
use App\Dominio\Repositorios\Contract\ContractQueriesRepository;
use App\Application\Queries\Contract\Fronteiras\OutputBoundaryContract;

class ContractQueriesUseCaseImplementation implements ContractQueriesUseCase
{
    public function __construct(
        private readonly IdentificacaoUnica $businessID,
        private ContractQueriesRepository $_contractRepository,
    ){}

    public function getContracts(): OutputBoundaryContract
    {

        $resultContractsRepository = $this->_contractRepository->getContracts();

        $Contracts = new OutputBoundaryContract();

        if(is_array($resultContractsRepository) and count($resultContractsRepository) > 0){

            foreach($resultContractsRepository as $Contract){
                
                $ContractToList = new OutputContract(
                    code: $Contract['codigo'],
                    clientID: $Contract['client_id'],
                    bankAccountID: $Contract['conta_bancaria_id'],
                    value: (float) $Contract['valor'],
                    dayEmitBilling: (int) $Contract['dia_emissao_cobranca'],
                );

                $Contracts->add($ContractToList);
            }
        }

        return $Contracts;
    }
}