<?php

declare(strict_types=1);

namespace App\Application\Queries\Client;

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Queries\Client\Fronteiras\OutputClient;
use App\Dominio\Repositorios\Client\ClientQueriesRepository;
use App\Application\Queries\Client\Fronteiras\OutputBoundaryClients;

class ClientQueriesUseCaseImplementation implements ClientQueriesUseCase
{
    public function __construct(
        readonly private IdentificacaoUnica $businessID,
        private readonly ClientQueriesRepository $_clientRepository
    ){}

    public function getClients(): OutputBoundaryClients
    {

        $resultClientsRepository = $this->_clientRepository->getClients();

        $clients = new OutputBoundaryClients();

        if(count($resultClientsRepository) > 0){

            foreach($resultClientsRepository as $client){
                
                $clientToList = new OutputClient(
                    id: $client['code'],
                    nome: $client['name'],
                    telefone: $client['phone'] ?? '',
                    email: $client['email'],
                    documento: $client['type_register']
                );

                $clients->add($clientToList);
            }
        }

        return $clients;
    }
}