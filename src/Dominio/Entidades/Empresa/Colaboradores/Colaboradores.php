<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Empresa\Colaboradores;

class Colaboradores
{
	private array $colaboradores = [];

	public function adicionarColaborador(EntidadeColaborador $colaborador): void
	{
		$this->colaboradores[] = $colaborador;
	}

	public function obterColaboradores(): array
	{
		return $this->colaboradores;
	}
}