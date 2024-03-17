<?php

declare(strict_types=1);

namespace App\Configuracao;

use App\Application\Commands\Billing\BillingUseCase;
use App\Application\Commands\Billing\BillingUseCaseImplementation;
use App\Application\Commands\Log\Log;
use App\Application\Queries\Billing\BillingQueriesUseCase;
use App\Application\Queries\Billing\BillingQueriesUseCaseImplementation;
use App\Aplicacao\Compartilhado\HTTP\ClientHttp;
use App\Dominio\Entidades\Business\EntidadeEmpresarial;
use App\Dominio\Repositorios\BankAccount\BankAccountRepository;
use App\Dominio\Repositorios\Billing\BillingQueriesRepository;
use App\Dominio\Repositorios\Billing\BillingRepository;
use App\Dominio\Repositorios\ChartOfAccount\ChartOfAccountRepository;
use App\Dominio\Repositorios\Client\ClientRepository;
use App\Dominio\Repositorios\Contract\ContractRepository;
use App\Dominio\Repositorios\Token\TokenRepository;
use App\Infraestrutura\Repositorios\Billing\BillingQueriesRepositoryImplementation;
use App\Infraestrutura\Repositorios\Billing\BillingRepositoryImplementation;
use App\Shared\Environment;
use App\Shared\EventBus\EventBus;
use DI\Container;
use Exception;
use PDO;

$pathAutoloader = __DIR__.'/../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
    BillingUseCase::class => function($content)
    {
        return new BillingUseCaseImplementation(
            businessID: $content->get('businessID'),
            _clientRepository: $content->get(ClientRepository::class),
            _contractRepository: $content->get(ContractRepository::class),
            _billingRepository: $content->get(BillingRepository::class),
            _businessEntity: $content->get(EntidadeEmpresarial::class),
            _bankAccountRepository: $content->get(BankAccountRepository::class),
            _chartOfAccountRepository: $content->get(ChartOfAccountRepository::class),
            _tokenRepository: $content->get(TokenRepository::class),
            _clientHttp: $content->get(ClientHttp::class),
            _env: $content->get(Environment::class),
            eventBus: $content->get(EventBus::class),
            _log: $content->get(Log::class),
        );
    },
    BillingQueriesUseCase::class => function($content)
    {
        return new BillingQueriesUseCaseImplementation(
            businessID: $content->get('businessID'),
            _billingRepository: $content->get(BillingQueriesRepository::class),
        );
    },
    BillingQueriesRepository::class => function(Container $content)
    {
        return new BillingQueriesRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
    BillingRepository::class => function(Container $content)
    {
        return new BillingRepositoryImplementation(
            businessID: $content->get('businessID'),
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
];