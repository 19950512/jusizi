<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Api\Controladores\Empresa;

use App\Application\Commands\Business\BusinessUsecase;
use App\Application\Commands\Business\Fronteiras\InputBoundaryCreateNewColaborador;
use App\Application\Queries\Business\BusinessQueriesUsecase;
use App\Dominio\Entidades\Contract\ContractEntity;
use App\Dominio\Repositorios\Contract\ContractRepository;
use App\Dominio\Repositorios\Contract\Fronteiras\OutputGetContractByID;
use App\Infraestrutura\APIs\Api\Controladores\Middlewares\Controller;
use DI\Container;
use Exception;

class EmpresaController extends Controller
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

