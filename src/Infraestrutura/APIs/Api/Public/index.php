<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Api\Public;

require_once __DIR__ . '/../../../../Aplicacao/Compartilhado/Containers/Container.php';

use App\Aplicacao\Compartilhado\Containers\Container;
use App\Infraestrutura\APIs\Router;

$containerApp = Container::getInstance();

$container = $containerApp->get(null);

$router = new Router(
    request_uri: $_SERVER['REQUEST_URI'] ?? '',
    container: $container,
    apiName: 'Api'
);