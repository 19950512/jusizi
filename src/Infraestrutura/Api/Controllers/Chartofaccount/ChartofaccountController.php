<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Chartofaccount;

use Exception;
use DI\Container;
use App\Infra\Api\Controllers\Middlewares\Controller;
use App\Application\Commands\ChartOfAccount\ChartOfAccountUseCase;
use App\Application\Queries\ChartOfAccount\ChartOfAccountQueriesUseCase;
use App\Application\Commands\ChartOfAccount\Fronteiras\InputBoundaryCreateNewChartOfAccount;

class ChartofaccountController extends Controller
{

    private ChartOfAccountUseCase $_chartOfAccountUseCase;
    private ChartOfAccountQueriesUseCase $_chartOfAccountQueriesUseCase;

    public function __construct(
        private Container $container
    ){

        parent::__construct(
            container: $this->container
        );

        $this->_chartOfAccountUseCase = $this->container->get(ChartOfAccountUseCase::class);
        $this->_chartOfAccountQueriesUseCase = $this->container->get(ChartOfAccountQueriesUseCase::class);
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
            return $this->_createNewChartOfAccount($_POST);
        }

        if($this->method === 'GET'){
            return $this->_getAllChartOfAccount();
        }
    }


    private function _createNewChartOfAccount()
    {
        if(!isset($_POST['name'], $_POST['description']) OR empty($_POST['name']) OR empty($_POST['description'])){
            
            return $this->response([
                'statusCode' => 403,
                'message' => 'Parametros inexistentes ou vazios - Name ou Description'
            ]);
        }

        try {

            $paramsToken = new InputBoundaryCreateNewChartOfAccount(
                name: $_POST['name'],
                description: $_POST['description']
                
            );

            $this->_chartOfAccountUseCase->createNewChartOfAccount($paramsToken);

            return $this->response([
                'statusCode' => 200,
                'message' => 'Plano de contas cadastrado com sucesso.'
            ]);

        }catch(Exception $erro){

            return $this->response([
                'statusCode' => 403,
                'message' => $erro->getMessage()
            ]);
        }
    }

    private function _getAllChartOfAccount()
    {
        try {

            $chartOfAccount = $this->_chartOfAccountQueriesUseCase->getChartOfAccounts();

            return $this->response([
                'statusCode' => 200,
                'data' => $chartOfAccount->get()
            ]);

        }catch(Exception $erro){

            return $this->response([
                'statusCode' => 403,
                'message' => $erro->getMessage()
            ]);
        }
    }
}

