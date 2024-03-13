<?php

declare(strict_types=1);

namespace App\Application\Queries\BankAccount;

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Queries\BankAccount\Fronteiras\OutputBankAccount;
use App\Dominio\Repositorios\BankAccount\BankAccountQueriesRepository;
use App\Application\Queries\BankAccount\Fronteiras\OutputBoundaryBankAccounts;

class BankAccountQueriesUseCaseImplementation implements BankAccountQueriesUseCase
{
    public function __construct(
        readonly private IdentificacaoUnica $businessID,
        private BankAccountQueriesRepository $_bankAccountRepository,
    ){}

    public function getBankAccounts(): OutputBoundaryBankAccounts
    {

        $resultBankAccountsRepository = $this->_bankAccountRepository->getBankAccounts();

        $bankAccounts = new OutputBoundaryBankAccounts();

        if(is_array($resultBankAccountsRepository) and count($resultBankAccountsRepository) > 0){

            foreach($resultBankAccountsRepository as $BankAccount){
                
                $BankAccountToList = new OutputBankAccount(
                    id: $BankAccount['codigo'],
                    nome: $BankAccount['nome'],
                    conta: $BankAccount['conta'],
                    cedente: $BankAccount['cedente'],
                    agencia: $BankAccount['agencia'],
                    posto: $BankAccount['posto'],
                    cnab: $BankAccount['cnab'],
                    banco: $BankAccount['banco'],
                );

                $bankAccounts->add($BankAccountToList);
            }
        }

        return $bankAccounts;
    }
}