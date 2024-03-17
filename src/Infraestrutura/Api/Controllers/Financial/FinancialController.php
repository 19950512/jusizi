<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Financial;

use App\Application\Commands\Financial\Fronteiras\InputBoundaryPost;
use App\Application\Commands\Financial\FinancialUsecase;
use App\Application\Commands\Paciente\Fronteiras\InputNewPaciente;
use App\Application\Commands\Paciente\PacienteUsecase;
use App\Application\Queries\Financial\FinancialQuery;
use App\Application\Queries\Paciente\PacienteQueriesUsecase;
use Exception;
use DI\Container;
use App\Infra\Api\Controllers\Middlewares\Controller;

class FinancialController extends Controller
{

    private FinancialUsecase $_financialUsecase;

    private FinancialQuery $_financialQuery;

    public function __construct(
        private Container $container
    ){

        parent::__construct(
            container: $this->container
        );

        $this->_financialUsecase = $this->container->get(FinancialUsecase::class);
        $this->_financialQuery = $this->container->get(FinancialQuery::class);
    }

    public function index()
    {
        return $this->response([
            'statusCode' => 403,
            'message' => 'Endpoint não permitido.'
        ]);
    }
    public function client()
    {
        if($this->method !== 'POST' and $this->method !== 'GET'){
            return $this->response([
                'statusCode' => 403,
                'message' => 'Method não permitido, use POST ou GET'
            ]);
        }

        if($this->method == 'POST'){

            if(!isset($_POST['clientID'], $_POST['charofaccountID'], $_POST['value'], $_POST['description']) OR empty($_POST['clientID']) OR empty($_POST['charofaccountID']) OR empty($_POST['value']) OR empty($_POST['description'])){

                return $this->response([
                    'statusCode' => 422,
                    'message' => 'Parametros inexistentes ou vazios - clientID, charofaccountID, value ou description'
                ]);
            }

            try {

                $params = new InputBoundaryPost(
                    clientID: $_POST['clientID'],
                    charofaccountID: $_POST['charofaccountID'],
                    value: (float) $_POST['value'],
                    description: $_POST['description']
                );

                $this->_financialUsecase->post($params);

                return $this->response([
                    'statusCode' => 200,
                    'message' => 'Lançamento financeiro efetuado com sucesso.'
                ]);

            } catch (Exception $e) {
                return $this->response([
                    'statusCode' => 500,
                    'message' => $e->getMessage()
                ]);
            }
        }

        if($this->method == 'GET'){

            if(!isset($_GET['clientID']) OR empty($_GET['clientID'])){

                return $this->response([
                    'statusCode' => 422,
                    'message' => 'Parametros inexistentes ou vazios - clientID'
                ]);
            }

            try {

                return $this->response([
                    'statusCode' => 200,
                    'data' => $this->_financialQuery->getSaldoClient($_GET['clientID'])
                ]);

            } catch (Exception $e) {
                return $this->response([
                    'statusCode' => 500,
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
}

