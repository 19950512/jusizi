<?php

declare(strict_types=1);

namespace App\Aplicacao\Comandos\Autenticacao\Empresa\CadastrarEmpresa;

use App\Aplicacao\Comandos\Comando;
use App\Aplicacao\Comandos\Lidar;
use App\Dominio\Entidades\Empresa\EntidadeEmpresarial;
use App\Dominio\ObjetoValor\IdentificacaoUnica;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\EntradaFronteiraNovaEmpresa;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraEmpresa;
use App\Dominio\Repositorios\Autenticacao\RepositorioAutenticacao;
use Exception;
use Override;

readonly class LidarCadastrarEmpresa implements Lidar
{

	public function __construct(
		private RepositorioAutenticacao $repositorioAutenticacaoComando
	){}

	#[Override] public function lidar(Comando $comando): void
	{
		if(is_a($comando, ComandoCadastrarEmpresa::class)){

			$empresaNomeFantasia = $comando->obterNomeFantasia();
			$responsavelNomeCompleto = $comando->obterResponsavelNomeCompleto();
			$responsavelEmail = $comando->obterResponsavelEmail();
			$responsavelSenha = $comando->obterResponsavelSenha();
			$empresaCodigo = new IdentificacaoUnica();

			if($this->repositorioAutenticacaoComando->jaExisteContaComEsseEmail($responsavelEmail)){
				throw new Exception("Já existe uma conta com o e-mail informado. ($responsavelEmail)");
			}

			$saidaFronteiraEmpresa = new SaidaFronteiraEmpresa(
				empresaCodigo: $empresaCodigo->get(),
				nome: $empresaNomeFantasia,
				numeroDocumento: '',
			);
			$entidadeEmpresarial = EntidadeEmpresarial::instanciarEntidadeEmpresarial($saidaFronteiraEmpresa);

			try {

				$parametrosNovaEmpresa = new EntradaFronteiraNovaEmpresa(
					empresaCodigo: $entidadeEmpresarial->codigo->get(),
					apelido: $entidadeEmpresarial->apelido->get(),
				);
				$this->repositorioAutenticacaoComando->cadastrarNovaEmpresa($parametrosNovaEmpresa);

			}catch (Exception $erro){
				throw new Exception("Ops, não foi possível cadastrar a empresa {$entidadeEmpresarial->apelido->get()}. {$erro->getMessage()}");
			}

			$responsavel =


			$hashSenha = password_hash($responsavelSenha, PASSWORD_ARGON2I);
		}

		throw new Exception("Ops, não sei lidar com esse comando.");

		// ...
	}
}