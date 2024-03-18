<?php

namespace App\Infraestrutura\APIs\Autenticacao\Public;

use App\Configuracao\Container;
use App\Infraestrutura\APIs\Router;
use Exception;

$pathAutoloader = __DIR__.'/../../../../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
	throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

$containerApp = Container::getInstance();

$container = $containerApp->get(null);

new Router(
    request_uri: $_SERVER['REQUEST_URI'] ?? '',
    container: $container,
    apiName: 'Autenticacao'
);