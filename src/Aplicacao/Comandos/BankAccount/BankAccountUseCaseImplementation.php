<?php

declare(strict_types=1);

namespace App\Application\Commands\BankAccount;

use App\Application\Commands\Log\Discord;
use App\Application\Commands\Log\DiscordChannel;
use Exception;
use App\Application\Commands\Log\Log;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Dominio\ObjetoValor\TextoSimples;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Commands\Log\Enumerados\Level;
use App\Dominio\Entidades\Financial\BankApiEntity;
use App\Dominio\Entidades\Financial\BankInformation;
use App\Dominio\Entidades\Financial\BankAccountEntity;
use App\Application\Commands\BankAccount\BankAccountUseCase;
use App\Dominio\Repositorios\BankAccount\BankAccountRepository;
use App\Application\Commands\BankAccount\Fronteiras\InputNewBankAccount;
use App\Dominio\Repositorios\BankAccount\Fronteiras\InputExistsBankAccount;
use App\Dominio\Repositorios\BankAccount\Fronteiras\OutputGetBankAccountByID;
use App\Dominio\Repositorios\BankAccount\Fronteiras\InputNewBankAccountRepository;

class BankAccountUseCaseImplementation implements BankAccountUseCase
{
    public function __construct(
        readonly private IdentificacaoUnica $businessID,
        private BankAccountRepository $_BankAccountRepository,
        private Discord $discord,
    ){}

    public function newBankAccount(InputNewBankAccount $params): void
    {

        if(empty($params->name)){
            $mensagem = 'Informe o nome do novo BankAccounte.';

            $this->discord->send(DiscordChannel::get(Level::ERROR), $mensagem);

            throw new Exception($mensagem);
        }

        // try create a new BankAccount Entity
        try {

            $UUIDBankAccount = new IdentificacaoUnica();

            try {

                $nameBankAccount = new NomeCompleto($params->name);
            }catch(Exception $erro){
                $mensagem = 'Informe o nome da conta bancária válido.';

                $this->discord->send(DiscordChannel::get(Level::ERROR), $mensagem);

                throw new Exception($mensagem);
            }

            $entityParams = [
                'id' => $UUIDBankAccount->get(),
                'name' => $nameBankAccount->get(),
                'agencia' => $params->agenciaNumber,
                'conta' => $params->accountNumber,
                'cedente' => $params->cedenteNumber,
                'posto' => $params->posto,
                'cnab' => (int) $params->cnab,
            ];

            if(!isset(BankInformation::$bank[$_POST['banco']])){
                $mensagem = 'O Banco informado não existe em nosso sistema, use: '.implode(', ', array_keys(BankInformation::$bank)).'.';

                $this->discord->send(DiscordChannel::get(Level::ERROR), $mensagem);

                throw new Exception($mensagem);
            }

            $bankInformation = BankInformation::$bank[$_POST['banco']];

            $bankApiEntity = new BankApiEntity(
                webhookSubscribe: false,
                certificateKey: new TextoSimples(''),
                certificatePass: new TextoSimples(''),
                credentialSecret: new TextoSimples(''),
                credentialToken: new TextoSimples(''),
            );

            $paramsBankAccountData = new OutputGetBankAccountByID(
                id: $entityParams['id'],
                name: $entityParams['name'],
                bankName: $bankInformation['name'],
                bankCode: $bankInformation['code'],
                bankCodeIspb: $bankInformation['codeIspb'],
                agencia: $entityParams['agencia'],
                conta: $entityParams['conta'],
                cedente: $entityParams['cedente'],
                posto: $entityParams['posto'],
                cnab: (int) $entityParams['cnab'] ?? 240,
                diasBoletoPermanenciaAgencia: 0,
            );
            
            $BankAccountEntity = BankAccountEntity::buildBankAccountEntity($paramsBankAccountData);

        }catch(Exception $erro){

            $mensagem = $erro->getMessage();

            $this->discord->send(DiscordChannel::get(Level::CRITICAL), $mensagem);
    
            throw new Exception($mensagem);
        }

        $infoContaExistsParams = new InputExistsBankAccount(
            accountNumber: (string) $BankAccountEntity->conta,
            agenciaNumber: (string) $BankAccountEntity->agencia,
            cedenteNumber: (string) $BankAccountEntity->cedente,
            bank: $BankAccountEntity->bank->name->get(),
        );

        if($this->_BankAccountRepository->existBankAccount($infoContaExistsParams)){

            $mensagem = 'Esta conta bancária já está cadastrada.';

            $this->discord->send(DiscordChannel::get(Level::ERROR), $mensagem);

            throw new Exception($mensagem);
        }

        // Lets try persist a new BankAccountEntity
        $inputNewBankAccount = new InputNewBankAccountRepository(
            id: $BankAccountEntity->id->get(),
            name: $BankAccountEntity->name->get(),
            accountNumber: (string) $BankAccountEntity->conta,
            agenciaNumber: (string) $BankAccountEntity->agencia,
            cedenteNumber: (string) $BankAccountEntity->cedente,
            posto: (string) $BankAccountEntity->posto,
            cnab: (string) $BankAccountEntity->cnab,
            bank: $BankAccountEntity->bank->name->get(),
            businessID: $this->businessID->get(),
        );

        try {
            
            $this->_BankAccountRepository->newBankAccount($inputNewBankAccount);
            $this->discord->send(DiscordChannel::get(Level::INFO), "Conta bancária criada com sucesso. ({$inputNewBankAccount->name}, {$inputNewBankAccount->bank} - ID: {$inputNewBankAccount->id} - BusinessID: {$inputNewBankAccount->businessID})");

        }catch(Exception $erro){

            $this->discord->send(DiscordChannel::get(Level::CRITICAL), $erro->getMessage());

            throw new Exception($erro->getMessage());
        }
    }
}