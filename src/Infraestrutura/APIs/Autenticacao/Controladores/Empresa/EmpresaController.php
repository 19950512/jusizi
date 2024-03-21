<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Autenticacao\Controladores\Empresa;

use App\Aplicacao\Comandos\Autenticacao\Empresa\CadastrarEmpresa\ComandoCadastrarEmpresa;
use DI\Container;

final readonly class EmpresaController
{


    public function __construct(
		private Container $container
    ){}

    public function index(): void
    {

		$comando = new ComandoCadastrarEmpresa(
			nomeFantasia: $_POST['nome_fantasia'] ?? '',
			responsavelNomeCompleto: $_POST['responsavel_nome_completo'] ?? '',
			responsavelEmail: $_POST['responsavel_email'] ?? '',
			responsavelSenha: $_POST['responsavel_senha'] ?? ''
		);

		$comando->executar();

		echo json_encode([
			'mensagem' => 'Empresa criada com sucesso',
			'empresaID' => $empresaID
		]);
    }
}

