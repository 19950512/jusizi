<?php

declare(strict_types=1);

namespace App\Application\Queries\Business;

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Queries\Business\Fronteiras\InputBoundaryQuerieCreateAccount;
use App\Application\Queries\Business\Fronteiras\OutputColaborador;
use App\Application\Queries\Business\Fronteiras\OutputGetColaboradores;
use App\Dominio\Entidades\EventEntity;
use Exception;
use Kreait\Firebase\Database;
use Kreait\Firebase\Factory;

class BusinessQueriesUsecaseImplementation implements BusinessQueriesUsecase
{

    private Database $_database;
    public function __construct(
        readonly private IdentificacaoUnica $businessID,
    ){

        $pathFirebaseCredentials = __DIR__.'/../../../Config/Certificados/firebase_credentials.json';
        if(!is_file($pathFirebaseCredentials))
            throw new Exception('Credenciais do Firebase não encontradas.');

        $factory = (new Factory())->withServiceAccount($pathFirebaseCredentials);

        $this->_database = $factory->createDatabase();
    }
    public function getAllColaboradores(): OutputGetColaboradores
    {

        $colaboradores = $this->_database->getReference("{$this->businessID->get()}/business_colaboradores")->getSnapshot()->getValue() ?? [];

        $colaboradores = array_values($colaboradores);

        $outputGetColaboradores = new OutputGetColaboradores();

        foreach($colaboradores as $colaborador){
            $outputGetColaboradores->add(
                new OutputColaborador(
                    code: $colaborador['code'] ?? '',
                    name: $colaborador['name'] ?? '',
                    email: $colaborador['email'] ?? '',
                    phone: $colaborador['phone'] ?? '',
                )
            );
        }

        return $outputGetColaboradores;
    }

    private function _event(EventEntity $params): void
    {

        $this->_database->getReference("{$this->businessID->get()}/business_colaboradores_eventos")->push([
            'event_type' => $params->type,
            'colaborador_id'   => $params->id,
            'event_name' => $params->name,
            'event_description' => $params->description,
            'event_params' => $params->data
        ]);
    }

    public function createAccount(InputBoundaryQuerieCreateAccount $params): void
    {

        $this->_database->getReference($this->businessID->get().'/business_colaboradores/'.$params->code)->set(
            [
                'code' => $params->code,
                'business' => $params->business->toArray(),
                'name' => $params->name,
                'email' => $params->email,
                'phone' => $params->phone,
                'created_at' => date('Y-m-d H:i:s'),
            ]
        );

        $event = new EventEntity(
            type: 'CREATE',
            id: $params->code,
            name: 'CREATE_COLABORADOR_BUSINESS',
            description: "Colaborador {$params->name} criado com sucesso no business {$params->business->code} - {$params->business->tradeName->get()} as ".date('Y-m-d H:i:s'),
            data: json_encode($params->toArray())
        );

        $this->_event(params: $event);
    }
}