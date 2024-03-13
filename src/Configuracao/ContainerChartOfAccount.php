<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use Exception;
use DI\Container;
use App\Application\Commands\Log\Log;
use App\Application\Commands\ChartOfAccount\ChartOfAccountUseCase;
use App\Dominio\Repositorios\ChartOfAccount\ChartOfAccountRepository;
use App\Application\Queries\ChartOfAccount\ChartOfAccountQueriesUseCase;
use App\Dominio\Repositorios\ChartOfAccount\ChartOfAccountQueriesRepository;
use App\Application\Commands\ChartOfAccount\ChartOfAccountUseCaseImplementation;
use App\Infraestrutura\Repositorios\ChartOfAccount\ChartOfAccountRepositoryImplementation;
use App\Application\Queries\ChartOfAccount\ChartOfAccountQueriesUseCaseImplementation;
use App\Infraestrutura\Repositorios\ChartOfAccount\ChartOfAccountQueriesRepositoryImplementation;

$pathAutoloader = __DIR__.'/../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
    ChartOfAccountUseCase::class => function($content)
    {
        return new ChartOfAccountUseCaseImplementation(
            businessID: $content->get('businessID'),
            _chartOfAccountRepository: $content->get(ChartOfAccountRepository::class),
            _log: $content->get(Log::class)
        );
    },
    ChartOfAccountQueriesUseCase::class => function($content)
    {
        return new ChartOfAccountQueriesUseCaseImplementation(
            businessID: $content->get('businessID'),
            _chartOfAccountRepository: $content->get(ChartOfAccountQueriesRepository::class),
        );
    },
    ChartOfAccountQueriesRepository::class => function(Container $content)
    {
        return new ChartOfAccountQueriesRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
    ChartOfAccountRepository::class => function(Container $content)
    {
        return new ChartOfAccountRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
];