<?php

declare(strict_types=1);

namespace App\Aplicacao\Compartilhado\Containers;

use App\Aplicacao\Compartilhado\Ambiente\Ambiente;
use App\Infraestrutura\Adaptadores\Ambiente\ImplementacaoAmbienteArquivo;
use Exception;
use PDO;
use PDOException;

$pathAutoloader = __DIR__ . '/../../../../vendor/autoload.php';

if(!is_file($pathAutoloader)){
    throw new Exception('Instale as dependências do projeto - Composer install');
}

require_once $pathAutoloader;

return [
    Ambiente::class => \DI\create(ImplementacaoAmbienteArquivo::class),
	PDO::class => function(Container $content)
    {

        $env = $content->get(Ambiente::class);

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
];