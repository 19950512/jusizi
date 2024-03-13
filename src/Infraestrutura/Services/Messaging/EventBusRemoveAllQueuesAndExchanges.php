<?php

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Aplicacao\Compartilhado\Mensageria\Mensageria;
use App\Config\Containerapp;

$containerApp = Containerapp::getInstance();

$container = $containerApp->get([
    'EVENT_BUS_HOST' => 'localhost',
    'DB_HOST' => 'localhost',
    'DB_PORT' => '8032'
]);

$eventBus = $container->get(Mensageria::class);

try {

    echo date('m/d/Y H:i:s a', time()) . " [x] Iniciando remoção de todas as queues e exchanges\n";

    $eventBus->removeExchanges();

    $eventBus->removeQueues();

    echo date('m/d/Y H:i:s a', time()) . " [x] Fim da remoção\n";

}catch(Exception $e){
    echo "{$e->getMessage()}\n";
}