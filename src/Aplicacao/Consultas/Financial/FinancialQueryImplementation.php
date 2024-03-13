<?php

declare(strict_types=1);

namespace App\Application\Queries\Financial;

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Dominio\Entidades\Business\EntidadeUsuarioLogado;
use App\Dominio\Entidades\EventEntity;
use App\Dominio\Repositorios\Financial\Fronteiras\InputBoundaryPostRepository;
use App\Shared\Environment;
use Exception;
use Kreait\Firebase\Database;
use Kreait\Firebase\Factory;

class FinancialQueryImplementation implements FinancialQuery
{
    private Database $_database;
    function __construct(
        private Environment                    $environment,
        readonly private IdentificacaoUnica                  $businessID,
        readonly private EntidadeUsuarioLogado $userLoggedEntity,
    ){

        $pathFirebaseCredentials = __DIR__.'/../../../Config/Certificados/firebase_credentials.json';
        if(!is_file($pathFirebaseCredentials))
            throw new Exception('Credenciais do Firebase não encontradas.');

        $factory = (new Factory())->withServiceAccount($pathFirebaseCredentials);

        $this->_database = $factory->createDatabase();
    }

    public function post(InputBoundaryPostRepository $params): void
    {
        $this->_database->getReference("{$this->businessID->get()}/caixa_movimentacoes/{$params->clientID}")->push([
            'movimentacao_code' => $params->code,
            'client_id' => $params->clientID,
            'charofaccount_id' => $params->charofaccountID,
            'value' => $params->value,
            'saldo_anterior' => $params->saldoAnterior,
            'saldo_atual' => $params->saldoAtual,
            'description' => $params->description,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $saveEvent = new EventEntity(
                type: 'CREATE',
                id: $params->code,
                name: 'CREATE_MOVIMENTACAO',
                description: $this->userLoggedEntity->name->get() . ' criou uma movimentação as ' . date('H:i:s').' do dia '.date('d/m/Y'),
                data: json_encode(
                    [
                        'movimentacao_code' => $params->code,
                        'client_id' => $params->clientID,
                        'charofaccount_id' => $params->charofaccountID,
                        'value' => $params->value,
                        'saldo_anterior' => $params->saldoAnterior,
                        'saldo_atual' => $params->saldoAtual,
                        'description' => $params->description,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
            )
        );

        $this->_event($saveEvent);
    }

    public function getSaldoClient(string $clientID): float
    {

        $movimentacoes = $this->_database->getReference("{$this->businessID->get()}/saldo_cliente/$clientID")->getSnapshot()->getValue() ?? [];

        $movimentacoes = array_values($movimentacoes);

        return $movimentacoes[0];
    }

    public function saveSaldoAtual(string $clientID, float $saldoAtual): void
    {
        $this->_database->getReference("{$this->businessID->get()}/saldo_cliente/$clientID/saldo_atual")->set($saldoAtual);
    }

    private function _event(EventEntity $params): void
    {

        $this->_database->getReference("{$this->businessID->get()}/caixa_movimentacoes_events")->push([
            'event_type' => $params->type,
            'movimentacao_id'   => $params->id,
            'event_name' => $params->name,
            'event_description' => $params->description,
            'event_params' => $params->data
        ]);
    }
}