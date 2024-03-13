<?php

declare(strict_types=1);

namespace App\Config;

use App\Application\Commands\Log\Discord;
use App\Application\Queries\Business\BusinessQueriesUsecase;
use PDO;
use Exception;
use DI\Container;
use App\Shared\Environment;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Autenticacao\AuthUseCase;
use App\Dominio\Repositorios\Autenticacao\AuthRepository;
use App\Application\Commands\Autenticacao\AuthUseCaseImplementation;
use App\Infra\Repositories\Autenticacao\AuthRepositoryImplementation;

$pathAutoloader = __DIR__.'/../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
    AuthUseCase::class => function(Container $content)
    {
        return new AuthUseCaseImplementation(
            _authRepository: $content->get(AuthRepository::class),
            discord: $content->get(Discord::class),
            _env: $content->get(Environment::class),
        );
    },
    AuthRepository::class => function(Container $content)
    {
        return new AuthRepositoryImplementation(
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
];