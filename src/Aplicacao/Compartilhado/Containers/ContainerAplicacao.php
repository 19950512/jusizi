<?php

declare(strict_types=1);

namespace App\Aplicacao\Compartilhado\Containers;

use App\Aplicacao\Compartilhado\Ambiente\Ambiente;
use App\Infraestrutura\Adaptadores\Ambiente\ImplementacaoAmbienteArquivo;
use Exception;

$pathAutoloader = __DIR__ . '/../../../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
    Ambiente::class => \DI\create(ImplementacaoAmbienteArquivo::class),
];