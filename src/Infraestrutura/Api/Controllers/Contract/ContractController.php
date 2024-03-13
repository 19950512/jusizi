<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Contract;

use Exception;
use DI\Container;
use App\Infra\Api\Controllers\Middlewares\Controller;
use App\Application\Commands\Contract\ContractUseCase;
use App\Application\Queries\Contract\ContractQueriesUseCase;
use App\Application\Commands\Contract\Fronteiras\InputBoundaryCreateNewContract;

class ContractController extends Controller
{

    private ContractUseCase $_contractUseCase;
    private ContractQueriesUseCase $_contractQueriesUseCase;

    public function __construct(
        private Container $container
    ){

        parent::__construct(
            container: $this->container
        );

        $this->_contractUseCase = $this->container->get(ContractUseCase::class);
        $this->_contractQueriesUseCase = $this->container->get(ContractQueriesUseCase::class);
    }

    public function index()
    {

        if($this->method !== 'POST' and $this->method !== 'GET'){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Method não permitido, use POST ou GET'
            ]);
        }

        if($this->method === 'POST'){
            return $this->_createNewContract($_POST);
        }

        if($this->method === 'GET'){
            return $this->_getAllContract();
        }
    }


    private function _createNewContract()
    {
        if(!isset($_POST['clientID'], $_POST['conta_bancaria_id']) OR empty($_POST['clientID']) OR empty($_POST['conta_bancaria_id'])){
            
            return $this->response([
                'statusCode' => 403,
                'message' => 'Parametros inexistentes ou vazios - conta_bancaria_id ou clientID'
            ]);
        }

        try {

            $paramsToken = new InputBoundaryCreateNewContract(
                clientID: $_POST['clientID'],
                diaEmissaoCobranca: (string) $_POST['dia_emissao_cobranca'],
                contaBancariaID: $_POST['conta_bancaria_id'],
                valor: (string) $_POST['valor']
            );

            $this->_contractUseCase->createNewContract($paramsToken);

            return $this->response([
                'statusCode' => 200,
                'message' => 'Contrato criado com sucesso.'
            ]);

        }catch(Exception $erro){

            return $this->response([
                'statusCode' => 403,
                'message' => $erro->getMessage()
            ]);
        }
    }

    private function _getAllContract()
    {
        try {

            $contract = $this->_contractQueriesUseCase->getContracts();

            return $this->response([
                'statusCode' => 200,
                'data' => $contract->get()
            ]);

        }catch(Exception $erro){

            return $this->response([
                'statusCode' => 403,
                'message' => $erro->getMessage()
            ]);
        }
    }
}

