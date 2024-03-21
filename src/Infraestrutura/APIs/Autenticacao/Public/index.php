<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Autenticacao\Public;

require_once __DIR__ . '/../../../../Aplicacao/Compartilhado/Containers/Container.php';

use App\Aplicacao\Compartilhado\Containers\Container;
use App\Infraestrutura\APIs\Router;

$containerApp = Container::getInstance();

$container = $containerApp->get(null);

new Router(
    request_uri: $_SERVER['REQUEST_URI'] ?? '',
    container: $container,
    apiName: 'Autenticacao'
);