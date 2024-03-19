<?php

declare(strict_types=1);

namespace App\Aplicacao\Comandos\Autenticacao\Login\EmailESenha;

use App\Aplicacao\Comandos\Comando;
use App\Aplicacao\Comandos\Lidar;
use Exception;
use Override;

readonly class LidarLoginEmailESenha implements Lidar
{
	#[Override] public function lidar(Comando $comando): void
	{

		if(!is_a($comando, ComandoLoginEmailESenha::class)){
			throw new Exception("Ops, não sei lidar com esse comando.");
		}

		$email = $comando->obterEmail();
		$senha = $comando->obterSenha();

		$hashSenha = password_hash($senha, PASSWORD_ARGON2I);

		// ...
	}
}