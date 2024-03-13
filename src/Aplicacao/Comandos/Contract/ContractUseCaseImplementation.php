<?php

declare(strict_types=1);

namespace App\Application\Commands\Contract;

use DateTime;
use Exception;
use App\Dominio\ObjetoValor\Value;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Dominio\ObjetoValor\Availability;
use App\Dominio\Entidades\Client\ClientEntity;
use App\Application\Commands\Log\Enumerados\Level;
use App\Dominio\Entidades\Contract\ContractEntity;
use App\Dominio\Entidades\Financial\BankAccountEntity;
use App\Dominio\Repositorios\Client\ClientRepository;
use App\Application\Commands\Contract\ContractUseCase;
use App\Dominio\Repositorios\Contract\ContractRepository;
use App\Dominio\Repositorios\BankAccount\BankAccountRepository;
use App\Dominio\Repositorios\Contract\Fronteiras\InputNewContract;
use App\Application\Commands\Contract\Fronteiras\InputBoundaryCreateNewContract;

class ContractUseCaseImplementation implements ContractUseCase
{
    public function __construct(
        private readonly IdentificacaoUnica $businessID,
        private ContractRepository $_contractRepository,
        private ClientRepository $_clientRepository,
        private BankAccountRepository $_bankAccountRepository,
        private Log $_log,
    ){}

    public function createNewContract(InputBoundaryCreateNewContract $params): void
    {
        
        $diaEmissaoCobranca = (int) $params->diaEmissaoCobranca;
        if(!is_numeric($diaEmissaoCobranca) || $diaEmissaoCobranca < 1 || $diaEmissaoCobranca > 31){

            $message = "O dia de emissão da cobrança deve ser um número entre 1 e 31.";
            $this->_log->log(
                level: Level::ERROR,
                message: $message,
            );
            throw new Exception($message);
        }

        try {

            $clientData = $this->_clientRepository->getClientByID($params->clientID);
            $clientEntity = ClientEntity::buildClientEntity($clientData);
        
        }catch(Exception $e) {

            $message = "O cliente informado não foi encontrado. ({$params->clientID})";
            $this->_log->log(
                level: Level::ERROR,
                message: $message,
            );
            throw new Exception($message);
        }

        /* if(!empty($params->contractID)){

            try {

                $contractData = $this->_contractRepository->getContractByID($params->contractID);
                $contractEntity = ContractEntity::buildContractEntity($contractData);

            }catch(Exception $e) {
                throw new Exception('O contrato informado não foi encontrado');
            }
        } */

        try {

            $bankAccountData = $this->_bankAccountRepository->getBankAccountByID($params->contaBancariaID, $this->businessID->get());
            $bankAccountEntity = BankAccountEntity::buildBankAccountEntity($bankAccountData);
        
        }catch(Exception $e) {
            $message = "A conta bancária informada não foi encontrada. ({$params->contaBancariaID})";
            $this->_log->log(
                level: Level::ERROR,
                message: $message,
            );
            throw new Exception($message);
        }

        try {

            $contractEntity = new ContractEntity(
                id: new IdentificacaoUnica(),
                client: $clientEntity,
                bankAccount: $bankAccountEntity,
                availability: new Availability(true),
                start_date: new DateTime(),
                value: new Value((float) $params->valor),
                diaEmissaoCobranca: $diaEmissaoCobranca,
                emitirNFSe: true,
                created_at: new DateTime(),
            );

            $codeContract = new IdentificacaoUnica();
            $newContractParams = new InputNewContract(
                businessID: $this->businessID->get(),
                clientID: $contractEntity->client->id->get(),
                bankAccountID: $contractEntity->bankAccount->id->get(),
                value: (string) $contractEntity->value->get(),
                atTime: $contractEntity->created_at->format('Y-m-d H:i:s'),
                code: $codeContract->get(),
                active: (string) $contractEntity->availability->get(),
                dayEmitBilling: (string) $contractEntity->diaEmissaoCobranca,
            );

            $this->_contractRepository->newContract($newContractParams);

            $this->_log->log(
                level: Level::INFO,
                message: "Contrato criado com sucesso. Código do contrato: {$codeContract->get()}",
            );

        }catch(Exception $e) {

            $message = "Não foi possível criar o contrato devido ao erro: {$e->getMessage()}";

            $this->_log->log(
                level: Level::CRITICAL,
                message: $message,
            );
            throw new Exception($message);
        }

    }
}