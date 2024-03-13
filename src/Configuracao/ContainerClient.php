<?php

declare(strict_types=1);

namespace App\Config;

use App\Application\Commands\Client\ClientUseCase;
use App\Application\Commands\Client\ClientUseCaseImplementation;
use App\Application\Queries\Client\ClientQueriesUseCaseImplementation;
use App\Dominio\Repositorios\Client\ClientRepository;
use App\Infra\Repositories\Client\ClientQueriesRepositoryImplementation;
use PDO;
use Exception;
use DI\Container;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Client\PacienteUseCase;
use App\Application\Queries\Client\ClientQueriesUseCase;
use App\Dominio\Repositorios\Client\ClientQueriesRepository;
use App\Application\Commands\Client\PacienteUseCaseImplementation;
use App\Infra\Repositories\Client\ClientRepositoryImplementation;
use App\Application\Queries\Client\PacienteQueriesUseCaseImplementation;

$pathAutoloader = __DIR__.'/../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
    ClientUseCase::class => function($content)
    {
        return new ClientUseCaseImplementation(
            businessID: $content->get('businessID'),
            _clientRepository: $content->get(ClientRepository::class),
            _log: $content->get(Log::class),
        );
    },
    ClientQueriesUseCase::class => function($content)
    {

        return new ClientQueriesUseCaseImplementation(
            businessID: $content->get('businessID'),
            _clientRepository: $content->get(ClientQueriesRepository::class),
        );
    },
    ClientQueriesRepository::class => function(Container $content)
    {
        return new ClientQueriesRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
    ClientRepository::class => function(Container $content)
    {
        return new ClientRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
];