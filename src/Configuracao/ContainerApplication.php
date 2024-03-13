<?php

declare(strict_types=1);

namespace App\Config;

use App\Application\Commands\Billing\BankAPI;
use App\Application\Commands\Log\Discord;
use App\Application\Commands\Log\Log;
use App\Application\Commands\NFSe\NFSeAPI;
use App\Aplicacao\Compartilhado\HTTP\ClientHttp;
use App\Dominio\Entidades\Business\EntidadeEmpresarial;
use App\Dominio\Repositorios\Token\TokenRepository;
use App\Dominio\ObjetoValor\Endereco\Endereco;
use App\Dominio\ObjetoValor\Endereco\CEP;
use App\Dominio\ObjetoValor\Endereco\Pais;
use App\Dominio\ObjetoValor\Endereco\Localizacao\Latitude;
use App\Dominio\ObjetoValor\Endereco\Localizacao\Localization;
use App\Dominio\ObjetoValor\Endereco\Localizacao\Longitude;
use App\Dominio\ObjetoValor\Endereco\Estado;
use App\Dominio\ObjetoValor\CNPJ;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Dominio\ObjetoValor\HostIP;
use App\Dominio\ObjetoValor\TextoSimples;
use App\Infraestrutura\Adaptadores\Billing\InterImplementation;
use App\Infraestrutura\Adaptadores\HTTP\ImplementacaoCurlClienteHTTP;
use App\Infraestrutura\Adaptadores\Log\DiscordImplementation;
use App\Infraestrutura\Adaptadores\Log\FileLogAdapter;
use App\Infraestrutura\Adaptadores\NFSe\VincoNFSeImplementation;
use App\Infra\EventBus\RabbitMQImplementation;
use App\Infraestrutura\Repositorios\Token\TokenRepositoryImplementation;
use App\Shared\Environment;
use App\Shared\EnvironmentImplementation;
use App\Shared\EventBus\EventBus;
use DI\Container;
use Exception;
use PDO;
use PDOException;

$pathAutoloader = __DIR__.'/../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
    Environment::class => \DI\create(EnvironmentImplementation::class),
    PDO::class => function(Container $content)
    {

        $env = $content->get(Environment::class);
       
        try {

            //$linkConexao = "pgsql:host={$env::get('DB_HOST')};dbname={$env::get('DB_DATABASE')};user={$env::get('DB_USERNAME')};password={$env::get('DB_PASSWORD')};port={$env::get('DB_PORT')}";
            $linkConexao = "pgsql:host={$content->get('DB_HOST')};dbname={$env::get('DB_DATABASE')};user={$env::get('DB_USERNAME')};password={$env::get('DB_PASSWORD')};port={$content->get('DB_PORT')}";

            $PDO = new PDO($linkConexao);
            $PDO->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
            $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $PDO;
        }catch (PDOException $erro){

            $message = $erro->getMessage();

            dd($message);

            if($message == 'could not find driver'){
                die('Não foi encontrado o Driver do PDO.');
            }

            header("HTTP/1.0 500 Connection");
            echo file_get_contents(__DIR__.'/sem_conexao.html');
            exit;
        }
    },
    Discord::class => function(Container $content)
    {
        return new DiscordImplementation(
            _clientHTTP: $content->get(ClientHttp::class),
            env: $content->get(Environment::class),
        );
    },
    Log::class => function(Container $content)
    {
        return new FileLogAdapter(
            discord: $content->get(Discord::class),
        );
    },
    ClientHttp::class => function(Container $content)
    {
        return new ImplementacaoCurlClienteHTTP();
    },
    EventBus::class => function(Container $content)
    {

        return new RabbitMQImplementation(
            host: $content->get('EVENT_BUS_HOST'),
            env: $content->get(Environment::class),
        );
    },
    NFSeAPI::class => function(Container $content)
    {
        return new VincoNFSeImplementation();
    },
    TokenRepository::class => function(Container $content)
    {
        return new TokenRepositoryImplementation(
            pdo: $content->get(PDO::class),
            log: $content->get(Log::class),
        );
    },
];