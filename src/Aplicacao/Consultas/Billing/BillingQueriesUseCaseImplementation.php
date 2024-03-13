<?php

declare(strict_types=1);

namespace App\Application\Queries\Billing;

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Queries\Billing\Fronteiras\OutputBilling;
use App\Dominio\Repositorios\Billing\BillingQueriesRepository;
use App\Application\Queries\Billing\Fronteiras\OutputBoundaryBillings;

class BillingQueriesUseCaseImplementation implements BillingQueriesUseCase
{
    public function __construct(
        readonly private IdentificacaoUnica $businessID,
        private BillingQueriesRepository $_billingRepository,
    ){}

    public function getBillings(): OutputBoundaryBillings
    {

        $resultBillingsRepository = $this->_billingRepository->getBillings();

        $Billings = new OutputBoundaryBillings();

        if(is_array($resultBillingsRepository) and count($resultBillingsRepository) > 0){

            foreach($resultBillingsRepository as $Billing){
                
                $BillingToList = new OutputBilling(
                    id: $Billing['codigo'],
                    contractID: $Billing['contract_id'],
                    chartofaccountID: $Billing['plano_de_contas_id'],
                    descricao: $Billing['descricao'],
                    valor: (float) $Billing['valor'],
                );

                $Billings->add($BillingToList);
            }
        }

        return $Billings;
    }
}