<?php

declare(strict_types=1);

namespace App\Aplicacao\Compartilhado\Containers;

use App\Aplicacao\Comandos\Autenticacao\Empresa\CadastrarEmpresa\LidarCadastrarEmpresa;
use App\Aplicacao\Compartilhado\Ambiente\Ambiente;
use App\Dominio\Repositorios\Autenticacao\RepositorioAutenticacao;
use App\Dominio\Repositorios\Empresa\RepositorioEmpresa;
use App\Infraestrutura\Adaptadores\Ambiente\ImplementacaoAmbienteArquivo;
use App\Infraestrutura\Repositorios\Autenticacao\ImplementacaoRepositorioAutenticacao;
use App\Infraestrutura\Repositorios\Empresa\ImplementacaoRepositorioEmpresa;
use Exception;
use GPBMetadata\Google\Api\Log;
use PDO;

$pathAutoloader = __DIR__ . '/../../../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
	RepositorioAutenticacao::class => function(Container $container)
	{
		return new ImplementacaoRepositorioAutenticacao(
		 pdo: $this->container->get(PDO::class),
		 log: $this->container->get(Log::class)
		);
	},
	LidarCadastrarEmpresa::class => function(Container $container)
	{
		return new LidarCadastrarEmpresa(
		    repositorioAutenticacaoComando: $this->container->get(RepositorioAutenticacao::class),
		);
	}

];
