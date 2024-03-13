<?php

declare(strict_types=1);

namespace App\Config;

use App\Application\Commands\Log\Discord;
use PDO;
use Exception;
use DI\Container;
use App\Application\Commands\Log\Log;
use App\Application\Commands\BankAccount\BankAccountUseCase;
use App\Dominio\Repositorios\BankAccount\BankAccountRepository;
use App\Application\Queries\BankAccount\BankAccountQueriesUseCase;
use App\Dominio\Repositorios\BankAccount\BankAccountQueriesRepository;
use App\Application\Commands\BankAccount\BankAccountUseCaseImplementation;
use App\Infra\Repositories\BankAccount\BankAccountRepositoryImplementation;
use App\Application\Queries\BankAccount\BankAccountQueriesUseCaseImplementation;
use App\Infra\Repositories\BankAccount\BankAccountQueriesRepositoryImplementation;

$pathAutoloader = __DIR__.'/../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
    BankAccountUseCase::class => function($content)
    {
        return new BankAccountUseCaseImplementation(
            businessID: $content->get('businessID'),
            _BankAccountRepository: $content->get(BankAccountRepository::class),
            discord: $content->get(Discord::class)
        );
    },
    BankAccountQueriesUseCase::class => function($content)
    {
        return new BankAccountQueriesUseCaseImplementation(
            businessID: $content->get('businessID'),
            _bankAccountRepository: $content->get(BankAccountQueriesRepository::class)
        );
    },
    BankAccountQueriesRepository::class => function(Container $content)
    {
        return new BankAccountQueriesRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
    BankAccountRepository::class => function(Container $content)
    {
        return new BankAccountRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
];