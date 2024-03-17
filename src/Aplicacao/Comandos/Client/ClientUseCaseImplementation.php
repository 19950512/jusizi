<?php

declare(strict_types=1);

namespace App\Application\Commands\Client;

use App\Application\Commands\Client\Fronteiras\InputNewClient;
use App\Application\Commands\Client\Fronteiras\InputUpdateClient;
use App\Application\Commands\Log\Enumerados\Level;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Dominio\Entidades\Client\ClientEntity;
use App\Dominio\Repositorios\Client\Fronteiras\InputNewClient as InputNewClientRepsitory;
use App\Dominio\Repositorios\Client\Fronteiras\InputUpdateClientRepository;
use App\Dominio\Repositorios\Client\Fronteiras\OutputGetClientByID;
use App\Dominio\Repositorios\Client\ClientRepository;
use App\Dominio\ObjetoValor\CNPJ;
use App\Dominio\ObjetoValor\CPF;
use App\Dominio\ObjetoValor\Email;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Dominio\ObjetoValor\Phone;
use Exception;

class ClientUseCaseImplementation implements ClientUseCase
{
    public function __construct(
        private readonly IdentificacaoUnica $businessID,
        private ClientRepository $_clientRepository,
        private Log $_log,
    ){}

    public function newClient(InputNewClient $params): void
    {

        if(empty($params->name)){
            $mensagem = "Informe o nome do novo cliente.";
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        if(empty($params->email)){
            $mensagem = 'Informe o e-mail do novo cliente.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        if(empty($params->phone)){
            $mensagem = 'Informe o telefone do novo cliente.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        if(empty($params->document)){
            $mensagem = 'Informe o número do documento do novo cliente.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        // try create a new Client Entity
        try {

            $UUIDClient = new IdentificacaoUnica();

            try {

                $nameClient = new NomeCompleto($params->name);
            }catch(Exception $erro){
                $mensagem = "Informe um nome de cliente válido. ({$params->name})";
                $this->_log->log(Level::ERROR, $mensagem);
                throw new Exception($mensagem);
            }

            try {
                $phoneClient = new Phone($params->phone);
            }catch(Exception $erro){
                $mensagem = "Informe um número de telefone válido. ({$params->phone})";
                $this->_log->log(Level::ERROR, $mensagem);
                throw new Exception($mensagem);
            }
           
            try {
                $emailClient = new Email($params->email);
            }catch(Exception $erro){
                $mensagem = "Informe e-mail válido. ({$params->email})";
                $this->_log->log(Level::ERROR, $mensagem);
                throw new Exception($mensagem);
            }

            try {

                $document = match(CPF::validation($params->document)){
                    true => new CPF($params->document),
                    default => new CNPJ($params->document)
                };

            }catch(Exception $erro){
                $mensagem = "Informe o número do documento válido. {$params->document}";
                $this->_log->log(Level::ERROR, $mensagem);
                throw new Exception($mensagem);
            }

            if($this->_clientRepository->existsClientByTypeRegister($document->get()))
            {
                $mensagem = "Já existe um cliente com esse documento, tente outro. ({$params->document})";
                $this->_log->log(Level::ERROR, $mensagem);
                throw new Exception($mensagem);
            }

            $entityParams = [
                'id' => $UUIDClient,
                'name' => $nameClient,
                'email' => $emailClient,
                'typeRegister' => $document,
                'phone' => $phoneClient,
            ];

            $clientEntity = new ClientEntity(...$entityParams);

        }catch(Exception $erro){

            $mensagem = $erro->getMessage();

            $this->_log->log(Level::CRITICAL, $mensagem);
            throw new Exception($mensagem);
        }

        $inputNewClient = new InputNewClientRepsitory(
            businessID: $this->businessID->get(),
            name: $clientEntity->name->get(),
            phone: $clientEntity->phone->get(),
            typeRegister: $clientEntity->typeRegister->get(),
            code: $clientEntity->id->get(),
            email: $clientEntity->email->get()
        );

        try {
            $this->_clientRepository->newClient($inputNewClient);

            $this->_log->log(Level::INFO, "Novo cliente cadastrado com sucesso. ({$clientEntity->id->get()}, {$clientEntity->name->get()}, {$clientEntity->email->get()}, {$clientEntity->typeRegister->get()})");

        }catch(Exception $erro){

            $this->_log->log(Level::CRITICAL, "Não foi possível salvar o novo cliente. {$inputNewClient->name}, {$inputNewClient->email}, {$inputNewClient->phone} | {$erro->getMessage()}");
            throw new Exception($erro->getMessage());
        }
    }

    public function updateClient(InputUpdateClient $params): void
    {

        if(empty($params->id)){
            $mensagem = 'Informe o código do cliente.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        if(empty($params->name)){
            $mensagem = 'Informe o nome completo do cliente.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        if(empty($params->email)){
            $mensagem = 'Informe o e-mail do cliente.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        if(empty($params->document)){
            $mensagem = 'Informe o número do documento do cliente.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        try {
            $clientData = $this->_clientRepository->getClientByID($params->id);
        }catch (Exception $erro){
            $mensagem = 'Não foi possível buscar informações do cliente, talvez não exista.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        $clientEntityAtual = ClientEntity::buildClientEntity($clientData);

        try {

            $UUIDClient = $clientEntityAtual->id;

            try {
                $nameClient = new NomeCompleto($params->name);
            }catch(Exception $erro){
                $mensagem = 'Informe um nome de cliente válido.';
                $this->_log->log(Level::ERROR, $mensagem);
                throw new Exception($mensagem);
            }
           
            try {
                $emailClient = new Email($params->email);
            }catch(Exception $erro){
                $mensagem = 'Informe e-mail válido.';
                $this->_log->log(Level::ERROR, $mensagem);
                throw new Exception($mensagem);
            }

            try {

                $document = match(CPF::validation($params->document)){
                    true => new CPF($params->document),
                    default => new CNPJ($params->document)
                };

            }catch(Exception $erro){
                $mensagem = 'Informe o número do documento válido.';
                $this->_log->log(Level::ERROR, $mensagem);
                throw new Exception($mensagem);
            }

            if($this->_clientRepository->existsClientByTypeRegister($document->get(), $UUIDClient->get()))
            {
                $mensagem = 'Já existe um cliente com esse documento, tente outro.';
                $this->_log->log(Level::ERROR, $mensagem);
                throw new Exception($mensagem);
            }

            $entityParams = new OutputGetClientByID(
                name: $nameClient->get(),
                typeRegister: $document->get(),
                code: $UUIDClient->get(),
                email: $emailClient->get(),
                phone: $clientEntityAtual->phone->get(),
            );

            $clientEntityNovo = ClientEntity::buildClientEntity($entityParams);

        }catch(Exception $erro){

            $mensagem = $erro->getMessage();

            $this->_log->log(Level::CRITICAL, $mensagem);
            throw new Exception($mensagem);
        }

        $diff = $clientEntityAtual->diff($clientEntityNovo);

        if(empty($diff)){
            $mensagem = 'Não foi possível atualizar o cliente, pois não houve alterações.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        $inputUpdateClient = new InputUpdateClientRepository(
            name: $clientEntityNovo->name->get(),
            phone: $clientEntityNovo->phone->get(),
            typeRegister: $clientEntityNovo->typeRegister->get(),
            code: $clientEntityNovo->id->get(),
            email: $clientEntityNovo->email->get()
        );

        try {
            $this->_clientRepository->updateClient($inputUpdateClient);
        }catch(Exception $erro){
            $this->_log->log(Level::CRITICAL, 'Não foi possível atualizar o cliente. | '.$erro->getMessage());
            throw new Exception($erro->getMessage());
        }
    }

    public function deleteClient(string $id): void
    {
        if(empty($id)){
            $mensagem = 'Informe o código do cliente.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        try {
            $clientData = $this->_clientRepository->getClientByID($id);
        }catch (Exception $erro){
            $mensagem = 'Não foi possível buscar informações do cliente, talvez não exista.';
            $this->_log->log(Level::ERROR, $mensagem);
            throw new Exception($mensagem);
        }

        $clientEntityAtual = ClientEntity::buildClientEntity($clientData);

        try {
            $this->_clientRepository->deleteClient($clientEntityAtual->id->get());
        }catch(Exception $erro){
            $this->_log->log(Level::CRITICAL, 'Não foi possível deletar o cliente. | '.$erro->getMessage());
            throw new Exception($erro->getMessage());
        }
    }
}