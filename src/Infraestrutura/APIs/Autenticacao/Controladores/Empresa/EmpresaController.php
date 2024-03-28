<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Autenticacao\Controladores\Empresa;

use App\Aplicacao\Comandos\Autenticacao\Empresa\CadastrarEmpresa\ComandoCadastrarEmpresa;
use App\Aplicacao\Comandos\Autenticacao\Empresa\CadastrarEmpresa\LidarCadastrarEmpresa;
use DI\Container;
use Exception;

final readonly class EmpresaController
{


    public function __construct(
		private Container $container
    ){}

    public function index(): void
    {

		try {

			$comando = new ComandoCadastrarEmpresa(
				nomeFantasia: $_POST['nome_fantasia'] ?? '',
				responsavelNomeCompleto: $_POST['responsavel_nome_completo'] ?? '',
				responsavelEmail: $_POST['responsavel_email'] ?? '',
				responsavelSenha: $_POST['responsavel_senha'] ?? ''
			);

			$comando->executar();

			$lidarCadastrarEmpresa = $this->container->get(LidarCadastrarEmpresa::class);

			$lidarCadastrarEmpresa->lidar($comando);

			header('Content-Type: application/json');
			header('HTTP/1.1 201 Created');

			echo json_encode([
				'mensagem' => 'Empresa cadastrada com sucesso'
			]);
			return;

		}catch (Exception $erro){
			header('Content-Type: application/json');
			header('HTTP/1.1 400 Bad Request');
			echo json_encode([
				'mensagem' => $erro->getMessage()
			]);
			return;
		}
    }
}

