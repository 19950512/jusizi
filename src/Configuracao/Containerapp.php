<?php

declare(strict_types=1);

namespace App\Config;

date_default_timezone_set('America/Sao_Paulo');

use Exception;
use DI\ContainerBuilder;

$pathAutoloader = __DIR__.'/../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

final class Containerapp
{

    private static ?self $instance = null;

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

        $EVENT_BUS_HOST = $args['EVENT_BUS_HOST'] ?? false;
        $DB_HOST = $args['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'postgres';
        $DB_PORT = $args['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '5432';

        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions([
            'EVENT_BUS_HOST' => $EVENT_BUS_HOST,
            'DB_HOST' => $DB_HOST,
            'DB_PORT' => $DB_PORT,
        ]);

        $containerApplication = $this->loader_container(__DIR__.'/ContainerApplication.php');
        $containerAuth = $this->loader_container(__DIR__.'/ContainerAuth.php');
        $containerClient = $this->loader_container(__DIR__.'/ContainerClient.php');
        $containerChartOfAccount = $this->loader_container(__DIR__.'/ContainerChartOfAccount.php');
        $containerBilling = $this->loader_container(__DIR__.'/ContainerBilling.php');
        $containerBankAccount = $this->loader_container(__DIR__.'/ContainerBankAccount.php');
        $containerContract = $this->loader_container(__DIR__.'/ContainerContract.php');
        $containerFinancial = $this->loader_container(__DIR__.'/ContainerFinancial.php');
        $containerBusiness = $this->loader_container(__DIR__.'/ContainerBusiness.php');



        $containerBuilder->addDefinitions([

            // Business
            ...$containerBusiness,

            // Application
            ...$containerApplication,

            // Auth
            ...$containerAuth,

            // Client
            ...$containerClient,

            // Chart Of Account
            ...$containerChartOfAccount,

            // Bank Account
            ...$containerBankAccount,

            // Contract
            ...$containerContract,

            // Billing
            ...$containerBilling,

            // Financial
            ...$containerFinancial,
        ]);

        try {
            return $containerBuilder->build();
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