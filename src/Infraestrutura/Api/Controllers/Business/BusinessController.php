<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Business;

use App\Application\Commands\Business\Fronteiras\InputBoundaryCreateNewColaborador;
use App\Application\Commands\Business\BusinessUsecase;
use App\Application\Queries\Business\BusinessQueriesUsecase;
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

class BusinessController extends Controller
{

    private BusinessQueriesUsecase $_businessQueriesUsecase;
    private BusinessUsecase $_businessUsecase;

    public function __construct(
        private Container $container
    ){

        parent::__construct(
            container: $this->container
        );

        $this->_businessQueriesUsecase = $this->container->get(BusinessQueriesUsecase::class);
        $this->_businessUsecase = $this->container->get(BusinessUsecase::class);
    }

    public function colaboradores()
    {

        if($this->method !== 'GET' and $this->method !== 'POST'){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Method não permitido, use GET ou POST'
            ]);
        }

        if($this->method === 'GET'){

            try {
    
                $colaboradores = $this->_businessQueriesUsecase->getAllColaboradores();
    
                return $this->response([
                    'statusCode' => 200,
                    'data' => $colaboradores->toArray()
                ]);
    
            }catch(Exception $erro){
    
                return $this->response([
                    'statusCode' => 403,
                    'message' => $erro->getMessage()
                ]);
            }
        }

        if($this->method === 'POST'){

            try {

                $params = new InputBoundaryCreateNewColaborador(
                    nome: $_POST['nome'],
                    email: $_POST['email'],
                );
                $this->_businessUsecase->createNewColaborador($params);
    
                return $this->response([
                    'statusCode' => 200,
                    'data' => 'Colaborador criado com sucesso.'
                ]);
    
            }catch(Exception $erro){
    
                return $this->response([
                    'statusCode' => 403,
                    'message' => $erro->getMessage()
                ]);
            }
        }

    }
}

