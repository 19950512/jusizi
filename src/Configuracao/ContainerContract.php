<?php

declare(strict_types=1);

namespace App\Config;

use App\Dominio\Repositorios\Client\ClientRepository;
use PDO;
use Exception;
use DI\Container;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Contract\ContractUseCase;
use App\Dominio\Repositorios\Contract\ContractRepository;
use App\Application\Queries\Contract\ContractQueriesUseCase;
use App\Dominio\Repositorios\BankAccount\BankAccountRepository;
use App\Dominio\Repositorios\Contract\ContractQueriesRepository;
use App\Application\Commands\Contract\ContractUseCaseImplementation;
use App\Infra\Repositories\Contract\ContractRepositoryImplementation;
use App\Application\Queries\Contract\ContractQueriesUseCaseImplementation;
use App\Infra\Repositories\Contract\ContractQueriesRepositoryImplementation;

$pathAutoloader = __DIR__.'/../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
    ContractUseCase::class => function($content)
    {
        return new ContractUseCaseImplementation(
            businessID: $content->get('businessID'),
            _contractRepository: $content->get(ContractRepository::class),
            _clientRepository: $content->get(ClientRepository::class),
            _bankAccountRepository: $content->get(BankAccountRepository::class),
            _log: $content->get(Log::class)
        );
    },
    ContractQueriesUseCase::class => function($content)
    {
        return new ContractQueriesUseCaseImplementation(
            businessID: $content->get('businessID'),
            _contractRepository: $content->get(ContractQueriesRepository::class),
        );
    },
    ContractQueriesRepository::class => function(Container $content)
    {
        return new ContractQueriesRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
    ContractRepository::class => function(Container $content)
    {
        return new ContractRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
];