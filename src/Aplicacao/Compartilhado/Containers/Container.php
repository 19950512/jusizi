<?php

declare(strict_types=1);

namespace App\Aplicacao\Compartilhado\Containers;

date_default_timezone_set('America/Sao_Paulo');

use Exception;
use DI\ContainerBuilder;

$pathAutoloader = __DIR__ . '/../../../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

final class Container
{

    private static self|null $instance = null;

	private \Di\Container|null $container = null;

    private function __construct()
    {
        // Construtor privado para evitar instanciação direta
    }

    public static function getInstance(): self
    {

        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(array | null $args)
    {

		if(is_a($this->container, \Di\Container::class)){
			return $this->container;
		}

        $EVENT_BUS_HOST = $args['EVENT_BUS_HOST'] ?? false;
        $DB_HOST = $args['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'postgres';
        $DB_PORT = $args['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '5432';

        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions([
            'EVENT_BUS_HOST' => $EVENT_BUS_HOST,
            'DB_HOST' => $DB_HOST,
            'DB_PORT' => $DB_PORT,
        ]);

		try {

			$containerApplication = $this->loader_container(__DIR__ . '/ContainerAplicacao.php');

	        $containerBuilder->addDefinitions([

	            // Application
	            ...$containerApplication,
	        ]);

			$this->container = $containerBuilder->build();

			return $this->container;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function loader_container(string $pathContainer): array
    {
        if(!is_file($pathContainer)){
            throw new Exception('Arquivo de configuração do container não encontrado.');
        }

        return require_once $pathContainer;
    }
}