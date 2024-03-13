<?php

declare(strict_types=1);

namespace App\Infraestrutura\Adaptadores\Mensageria;

use App\Aplicacao\Compartilhado\Mensageria\Enumerados\Event;
use App\Aplicacao\Compartilhado\Mensageria\Enumerados\Exchange;
use App\Aplicacao\Compartilhado\Mensageria\Enumerados\Queue;
use App\Aplicacao\Compartilhado\Mensageria\Mensageria;
use App\Shared\Environment;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMQImplementation implements Mensageria
{

    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(
        private string | bool $host,
        private Environment $env
    ){

        $host = $this->env->get('EVENT_BUS_HOST');
        if($this->host){
            $host = $this->host;
        }

        $port = $this->env->get('EVENT_BUS_PORT');
        $user = $this->env->get('EVENT_BUS_USER');
        $password = $this->env->get('EVENT_BUS_PASSWORD');
        $max_retry_connections = (int) $this->env->get('EVENT_BUS_MAX_RETRY_CONNECTIONS');
        $retry_delay_seconds = (int) $this->env->get('EVENT_BUS_RETRY_CONNECTIONS_DELAY_SECONDS');

        $attempts = 0;
        while ($attempts < $max_retry_connections) {
            try {
                $this->connection = new AMQPStreamConnection(
                    $host,
                    $port,
                    $user,
                    $password
                );
                break;
            } catch (AMQPIOException $e) {
                //echo "Erro de E/S: " . $e->getMessage() . "\n";
            } catch (AMQPRuntimeException $e) {
                //echo "Erro de tempo de execução: " . $e->getMessage() . "\n";
            } catch (Exception $e) {
                //echo "Erro desconhecido: " . $e->getMessage() . "\n";
            }

            $attempts++;
            if ($attempts < $max_retry_connections) {
                //echo "Tentando novamente em $retry_delay_seconds segundos...\n";
                sleep($retry_delay_seconds);
            } else {
                //echo "Limite máximo de tentativas de conexão excedido\n";
                break;
            }
        }

        $this->channel = $this->connection->channel();
    }

    public function publish(Event $event, string $message): void
    {

        $queue = $event->getQueue();

        $mensagem = new AMQPMessage(
            body: $message,
            properties: [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            ]
        );

        // Inicia a transação
       // $this->channel->tx_select();

        try {

            $this->channel->basic_publish(
                msg: $mensagem,
                routing_key: $queue->value
            );
            // Confirma a transação
           // $this->channel->tx_commit();

        }catch(Exception $e) {

            $erro = $e->getMessage();

            // Desfaz a transação
           // $this->channel->tx_rollback();
        }
    }

    public function subscribe(Event $event, callable $callback): void
    {
        $queue = $event->getQueue();

        $this->channel->basic_qos(
            prefetch_size: null,
            prefetch_count: 1, // Quantidade de mensagens que o consumidor pode receber por vez até que ele envie um ack
            a_global: null
        );

        try{

            $this->channel->basic_consume(
                queue: $queue->value,
                no_ack: false,
                callback: $callback
            );

            /*
             Não usar isso, da forma que eu imagino, essa pratica não é útil.
             while ($this->channel->is_consuming()) {
                $this->channel->wait(
                    timeout: 20
                );
            }*/

        }catch(Exception $e){

            $erro = $e->getMessage();

            if(str_contains($erro, 'NOT_FOUND - no queue')){

                throw new Exception("A fila {$queue->value} não existe");
            }

            throw new Exception($erro);
        }

        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }


    public function removeQueues(): void
    {
        $queues = Queue::getQueues();

        foreach($queues as $queue){
            $this->channel->queue_delete($queue['queue']->value);
        }
    }

    public function removeExchanges(): void
    {
        $exchanges = Exchange::getExchanges();

        foreach($exchanges as $exchange){
            $this->channel->exchange_delete($exchange['exchange']->value);
        }
    }

    public function createQueues(): void
    {
        // Create the exchanges
        $this->exchange_declares();

        // Create the queues
        $this->queue_declares();

        // Bind the queues to the exchanges
        $this->queue_binds();
    }

    private function exchange_declares(): void
    {

        foreach(Exchange::getExchanges() as $exchange){

            $this->channel->exchange_declare(
                exchange: $exchange['exchange']->value,
                type: $exchange['type'],
                durable: true,
                auto_delete: false
            );
        }
    }

    private function queue_declares(): void
    {

        foreach(Queue::getQueues() as $queue){

            $argumentsDLX = [];
            if(isset($queue['dlx']) and is_a($queue['dlx'], Exchange::class)){
                $argumentsDLX['x-dead-letter-exchange'] = $queue['dlx']->value;
                // Outros argumentos, se necessário
            }

            $this->channel->queue_declare(
                queue: $queue['queue']->value,
                durable: true,
                auto_delete: false,
                arguments: new AMQPTable($argumentsDLX)
            );
        }
    }

    private function queue_binds(): void
    {

        foreach(Queue::getBinds() as $bind){

            if(!is_a($bind['queue'], Queue::class) OR !is_a($bind['exchange'], Exchange::class)){
                throw new Exception("A fila {$bind['queue']->value} não pode ser vinculada a troca {$bind['exchange']->value}");
            }

            $this->channel->queue_bind(
                queue: $bind['queue']->value,
                exchange: $bind['exchange']->value,
            );
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
