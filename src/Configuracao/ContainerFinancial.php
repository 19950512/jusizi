<?php

declare(strict_types=1);

namespace App\Config;

use App\Application\Commands\Client\ClientUseCase;
use App\Application\Commands\Client\ClientUseCaseImplementation;
use App\Application\Commands\Financial\FinancialUsecase;
use App\Application\Commands\Financial\FinancialUsecaseImplementation;
use App\Application\Queries\Client\ClientQueriesUseCaseImplementation;
use App\Application\Queries\Financial\FinancialQuery;
use App\Application\Queries\Financial\FinancialQueryImplementation;
use App\Dominio\Entidades\Business\EntidadeUsuarioLogado;
use App\Dominio\Repositorios\ChartOfAccount\ChartOfAccountRepository;
use App\Dominio\Repositorios\Client\ClientRepository;
use App\Dominio\Repositorios\Financial\FinancialRepositoryCommand;
use App\Dominio\Repositorios\Financial\FinancialRepositoryQuery;
use App\Infra\Repositories\Client\ClientQueriesRepositoryImplementation;
use App\Infra\Repositories\Financial\FirebaseFinancialRepositoryQueryImplementation;
use App\Infra\Repositories\Financial\PostgreSQLFinancialRepositoryImplementation;
use App\Shared\Environment;
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
    FinancialQuery::class => function(Container $content)
    {
        return new FinancialQueryImplementation(
            environment: $content->get(Environment::class),
            businessID: $content->get('businessID'),
            userLoggedEntity: $content->get(EntidadeUsuarioLogado::class),
        );
    },
    FinancialRepositoryQuery::class => function(Container $content)
    {
        return new FirebaseFinancialRepositoryQueryImplementation(
            businessID: $content->get('businessID'),
            userLoggedEntity: $content->get(EntidadeUsuarioLogado::class),
        );
    },
    FinancialRepositoryCommand::class => function(Container $content)
    {
        return new PostgreSQLFinancialRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
        );
    },
    FinancialUsecase::class => function(Container $content)
    {
        return new FinancialUsecaseImplementation(
            businessID: $content->get('businessID'),
            clientRepository: $content->get(ClientRepository::class),
            financialQuery: $content->get(FinancialQuery::class),
            financialRepository: $content->get(FinancialRepositoryCommand::class),
            chartOfAccountRepository: $content->get(ChartOfAccountRepository::class)
        );
    },
];