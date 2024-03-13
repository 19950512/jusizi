<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\BankAccount;

use Exception;
use DI\Container;
use App\Infra\Api\Controllers\Middlewares\Controller;
use App\Application\Commands\BankAccount\BankAccountUseCase;
use App\Application\Queries\BankAccount\BankAccountQueriesUseCase;
use App\Application\Commands\BankAccount\Fronteiras\InputNewBankAccount;

class BankAccountController extends Controller
{

    private BankAccountUseCase $_bankAccountUsecase;
    private BankAccountQueriesUseCase $_bankAccountQueriesUsecase;

    public function __construct(
        private Container $container
    ){

        parent::__construct(
            container: $this->container
        );

        $this->_bankAccountUsecase = $this->container->get(BankAccountUseCase::class);
        $this->_bankAccountQueriesUsecase = $this->container->get(BankAccountQueriesUseCase::class);
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

            if(!isset($_POST['name'], $_POST['conta'],$_POST['cedente'],$_POST['posto'],$_POST['cnab'],$_POST['banco'],$_POST['agencia']) OR empty($_POST['name'])){
                
                return $this->response([
                    'statusCode' => 403,
                    'message' => 'Parametros inexistentes ou vazios - Name, Conta, Cedente, Posto, Cnab, Agencia ou Banco'
                ]);
            }
    
            try {
    
                $paramsToken = new InputNewBankAccount(
                    name: $_POST['name'],
                    agenciaNumber: $_POST['agencia'],
                    accountNumber: $_POST['conta'],
                    cedenteNumber: $_POST['cedente'],
                    posto: $_POST['posto'],
                    cnab: $_POST['cnab'],
                    bank: $_POST['banco'],
                );
    
                $this->_bankAccountUsecase->newBankAccount($paramsToken);
    
                return $this->response([
                    'statusCode' => 200,
                    'message' => 'Conta bancária criada com sucesso.'
                ]);
    
            }catch(Exception $erro){
    
                return $this->response([
                    'statusCode' => 403,
                    'message' => $erro->getMessage()
                ]);
            }
        }


        if($this->method === 'GET'){

            try {

                $banksAccounts = $this->_bankAccountQueriesUsecase->getBankAccounts();

                return $this->response([
                    'statusCode' => 200,
                    'data' => $banksAccounts->get()
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

