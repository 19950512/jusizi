<?php

declare(strict_types=1);

namespace App\Configuracao;

use App\Application\Commands\Billing\BillingUseCase;
use App\Application\Commands\Billing\BillingUseCaseImplementation;
use App\Application\Commands\Business\BusinessUsecase;
use App\Application\Commands\Business\BusinessUsecaseImplementation;
use App\Application\Commands\Log\Log;
use App\Application\Queries\Billing\BillingQueriesUseCase;
use App\Application\Queries\Billing\BillingQueriesUseCaseImplementation;
use App\Application\Queries\Business\BusinessQueriesUsecase;
use App\Application\Queries\Business\BusinessQueriesUsecaseImplementation;
use App\Aplicacao\Compartilhado\HTTP\ClientHttp;
use App\Dominio\Entidades\Business\EntidadeEmpresarial;
use App\Dominio\Repositorios\BankAccount\BankAccountRepository;
use App\Dominio\Repositorios\Billing\BillingQueriesRepository;
use App\Dominio\Repositorios\Billing\BillingRepository;
use App\Dominio\Repositorios\Business\BusinessRepository;
use App\Dominio\Repositorios\ChartOfAccount\ChartOfAccountRepository;
use App\Dominio\Repositorios\Client\ClientRepository;
use App\Dominio\Repositorios\Contract\ContractRepository;
use App\Dominio\Repositorios\Token\TokenRepository;
use App\Infraestrutura\Repositorios\Billing\BillingQueriesRepositoryImplementation;
use App\Infraestrutura\Repositorios\Billing\BillingRepositoryImplementation;
use App\Infraestrutura\Repositorios\Empresa\ImplementacaoRepositorioEmpresa;
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
    BusinessQueriesUsecase::class => function(Container $content)
    {
        return new BusinessQueriesUseCaseImplementation(
            businessID: $content->get('businessID'),
        );
    },
    BusinessUsecase::class => function(Container $content)
    {
        return new BusinessUsecaseImplementation(
            businessRepository: $content->get(BusinessRepository::class),
            _businessQueriesUsecase: $content->get(BusinessQueriesUsecase::class),
            businessID: $content->get('businessID'),
            businessEntity: $content->get(EntidadeEmpresarial::class),
        );
    },
    BusinessRepository::class => function(Container $content)
    {
        return new ImplementacaoRepositorioEmpresa(
            pdo: $content->get(PDO::class),
        );
    },
];