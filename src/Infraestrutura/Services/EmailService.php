#!/usr/bin/php
<?php

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__.'/../../../vendor/autoload.php';

use App\Config\Containerapp;
use App\Infra\EventBus\Event;
use App\Shared\EventBus\EventBus;
use App\Application\Commands\Log\Log;
use App\Infraestrutura\Adaptadores\Email\PHPMailerEmail;
use App\Application\Commands\Log\Enumerados\Level;

$containerApp = Containerapp::getInstance();

$container = $containerApp->get([
    'EVENT_BUS_HOST' => 'rabbitmq-master',
    'DB_HOST' => 'postgres',
    'DB_PORT' => '5432'
]);

$eventBus = $container->get(EventBus::class);
$log = $container->get(Log::class);

$maximumRetry = 5;
$callback = function($message) use ($maximumRetry, &$eventBus, &$log) {
    echo date('m/d/Y h:i:s a', time()) . " [x] Novo email: ", $message->body, "\n";
    echo "------------------------------------------------------------\n";

    $body = json_decode($message->body, true);

    $tentativasRetry = 1;
    if(isset($body['try_attempt']) and is_numeric($body['try_attempt'])){
        $tentativasRetry += $body['try_attempt'] ;
    }
    
    try {

        $emailService = new PHPMailerEmail();
        
        try {
    
            $nomeCliente = $body['nome'] ?? 'Cliente';
            $emailCliente = $body['email'] ?? '';
            $tituloEmail = $body['titulo'] ?? '';

            if(empty($emailCliente)){
                $tentativasRetry = $maximumRetry;
                $log->log(Level::ERROR, "Email do cliente não informado, tentativa de Retry excedida");
                throw new Exception("Email do cliente não informado, tentativa de Retry excedida");
            }

            if(empty($tituloEmail)){
                $tentativasRetry = $maximumRetry;
                $log->log(Level::ERROR, "Email sem título, tentativa de Retry excedida");
                throw new Exception("Email sem título, tentativa de Retry excedida");                
            }

            $resposta = $emailService->send(
                titulo: $tituloEmail,
                clienteEmail: $emailCliente,
                clienteNome: $nomeCliente
            );
    
            if($resposta === true){
                echo "Email enviado com sucesso\n";
                $log->log(Level::INFO, "Email enviado com sucesso para o cliente {$nomeCliente} - {$emailCliente}");
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
                return;
            }

            throw new Exception("Erro ao enviar o email: {$resposta}");
    
        }catch(Exception $e){
            throw new Exception("Exception --- Erro ao enviar o email: {$e->getMessage()}");
        }

    } catch(Exception $e) {

        // Ocorreu um erro ao enviar o email, vamos tentar novamente
        if($tentativasRetry >= $maximumRetry){

            try {

                $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
                
                $log->log(Level::CRITICAL, "Erro ao enviar o email: {$e->getMessage()} e tentativa de Retry excedida");
                echo "Erro ao enviar o email: {$e->getMessage()} e tentativa de Retry excedida\n";
                return;

            }catch(Exception $e){
                echo "Exception --- Erro ao enviar o email: {$e->getMessage()}\n";
            }
        }

        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);

        $body['try_attempt'] = 0;
        $body['try_attempt'] += $tentativasRetry;

        $eventBus->publish(
            event: Event::EnviarEmail,
            message: json_encode($body),
        );
    }
};

try {

    echo date('m/d/Y H:i:s a', time()) . " [x] Aguardando novos emails\n";
    
    $eventBus->subscribe(
        event: Event::EnviarEmail,
        callback: $callback
    );

}catch(Exception $e){
    echo "{$e->getMessage()}\n";
}


