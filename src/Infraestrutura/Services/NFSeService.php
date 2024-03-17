#!/usr/bin/php
<?php

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__.'/../../../vendor/autoload.php';

use App\Configuracao\Containerapp;
use App\Infra\EventBus\Event;
use App\Shared\EventBus\EventBus;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Log\Enumerados\Level;
use App\Application\Commands\NFSe\NFSeUseCase;
use App\Application\Commands\NFSe\Fronteiras\InputBoundaryEmitirNFSe;

$containerApp = Containerapp::getInstance();

$container = $containerApp->get([
    'EVENT_BUS_HOST' => 'rabbitmq-master',
    'DB_HOST' => 'postgres',
    'DB_PORT' => '5432'
]);

$eventBus = $container->get(EventBus::class);
$log = $container->get(Log::class);

$maximumRetry = 5;
$callback = function($message) use ($maximumRetry, &$container, &$eventBus, &$log) {
    echo date('m/d/Y h:i:s a', time()) . " [x] Novo NFS-e: ", $message->body, "\n";
    echo "------------------------------------------------------------\n";

    $body = json_decode($message->body, true);

    $tentativasRetry = 1;
    if(isset($body['try_attempt']) and is_numeric($body['try_attempt'])){
        $tentativasRetry += $body['try_attempt'] ;
    }
    
    try {

        
        try {
    
            $NFSeUsecase = $container->get(NFSeUseCase::class);

            $paramsNFSe = new InputBoundaryEmitirNFSe();

            $NFSeUsecase->emitirNFSe($paramsNFSe);

            $log->log(Level::INFO, "NFS-e criada com sucesso no Usecase.");
    
            echo "NFS-e criada com sucesso no Usecase\n";
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            return;

        }catch(Exception $e){

            $log->log(Level::ERROR, "Exception --- Erro ao criar a NFSe: {$e->getMessage()}");
            throw new Exception("Exception --- Erro ao criar a NFSe: {$e->getMessage()}");
        }

    } catch(Exception $e) {

        // Ocorreu um erro, vamos tentar novamente
        if($tentativasRetry >= $maximumRetry){

            try {

                $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);

                $log->log(Level::ERROR, "Erro ao criar a NFS-e: {$e->getMessage()} e tentativa de Retry excedida");
                
                echo "Erro ao criar a NFS-e: {$e->getMessage()} e tentativa de Retry excedida\n";
                return;

            }catch(Exception $e){
                echo "Exception --- Erro ao criar a NFS-e: {$e->getMessage()}\n";
            }
        }

        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);

        $body['try_attempt'] = 0;
        $body['try_attempt'] += $tentativasRetry;

        $eventBus->publish(
            event: Event::EmitirNfse,
            message: json_encode($body),
        );
    }
};

try {

    echo date('m/d/Y H:i:s a', time()) . " [x] Aguardando novas NFS-e\n";
    
    $eventBus->subscribe(
        event: Event::EmitirNfse,
        callback: $callback
    );

}catch(Exception $e){
    echo "{$e->getMessage()}\n";
}


