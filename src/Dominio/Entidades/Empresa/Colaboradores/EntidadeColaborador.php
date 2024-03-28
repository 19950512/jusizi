<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Empresa\Colaboradores;

use App\Dominio\ObjetoValor\Email;
use App\Dominio\ObjetoValor\IdentificacaoUnica;
use App\Dominio\ObjetoValor\NomeCompleto;

class EntidadeColaborador
{
	public function __construct(
		readonly public IdentificacaoUnica $codigo,
		public NomeCompleto $nomeCompleto,
		public Email $email,
	){}
}
