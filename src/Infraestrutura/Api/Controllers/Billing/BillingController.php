<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Billing;

use Exception;
use DI\Container;
use App\Dominio\Entidades\Contract\ContractEntity;
use App\Application\Commands\Billing\BillingUseCase;
use App\Infra\Api\Controllers\Middlewares\Controller;
use App\Application\Commands\Contract\ContractUseCase;
use App\Dominio\Repositorios\Contract\ContractRepository;
use App\Application\Queries\Billing\BillingQueriesUseCase;
use App\Application\Commands\Billing\Fronteiras\InputPushtBilling;
use App\Dominio\Repositorios\Contract\Fronteiras\OutputGetContractByID;

class BillingController extends Controller
{

    private BillingUseCase $_billingUseCase;
    private BillingQueriesUseCase $_billingQueriesUseCase;
    private ContractRepository $_contractRepository;

    public function __construct(
        private Container $container
    ){

        parent::__construct(
            container: $this->container
        );
        $this->_billingUseCase = $this->container->get(BillingUseCase::class);

        $this->_contractRepository = $this->container->get(ContractRepository::class);

        $this->_billingQueriesUseCase = $this->container->get(BillingQueriesUseCase::class);
    }

    public function emitBillingByContract()
    {

        if($this->method !== 'POST'){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Method não permitido, use POST'
            ]);
        }

        if(!isset($_POST['contractID']) OR empty($_POST['contractID'])){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Parametro contractID não encontrado ou vazio'
            ]);
        }

        $contractData = $this->_contractRepository->getContractByID($_POST['contractID']);
        $contractEntity = ContractEntity::buildContractEntity($contractData);

        try {

            $this->_billingUseCase->registerBilling($contractEntity);

            return $this->response([
                'statusCode' => 200,
                'message' => 'Cobrança emitida com sucesso.'
            ]);

        }catch(Exception $erro){

            return $this->response([
                'statusCode' => 403,
                'message' => $erro->getMessage()
            ]);
        }
    }

    public function push()
    {

        if($this->method !== 'POST' AND $this->method !== 'GET'){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Method não permitido, use POST ou GET'
            ]);
        }

        if($this->method === 'GET'){
            return $this->_getBilling();
        }

        if($this->method === 'POST'){
            return $this->_pushBilling($_POST);
        }
    }

    private function _getBilling()
    {

        $billings = $this->_billingQueriesUseCase->getBillings();

        return $this->response([
            'statusCode' => 200,
            'data' => $billings->get()
        ]);
    }

    private function _pushBilling($data)
    {

        if(!isset($data['charofaccountID'], $data['contractID'], $data['value'], $data['description']) OR empty($data['charofaccountID']) OR empty($data['contractID']) OR empty($data['value']) OR empty($data['description'])){

            return $this->response([
                'statusCode' => 403,
                'message' => 'Parametros inexistentes ou vazios - charofaccountID, contractID, value ou Description'
            ]);
        }

        try {

            $paramsBilling = new InputPushtBilling(
                chartOfAccountID: $data['charofaccountID'],
                contractID: $data['contractID'],
                value: (string) $data['value'],
                description: $data['description'],
            );

            $this->_billingUseCase->pushBilling($paramsBilling);

            return $this->response([
                'statusCode' => 200,
                'message' => 'Cobrança lançada com sucesso'
            ]);

        }catch(Exception $erro){

            return $this->response([
                'statusCode' => 403,
                'message' => $erro->getMessage()
            ]);
        }
    }
}

