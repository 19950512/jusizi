<?php

// Fontes::class

use App\Dominio\Entidades\Processo\Fontes\EntidadeFonte;
use App\Dominio\Entidades\Processo\Fontes\Fontes;
use App\Dominio\ObjetoValor\IdentificacaoUnica;

test('Deverá ser uma instância de Fontes', function () {
	$fontes = new Fontes();
	expect($fontes)->toBeInstanceOf(Fontes::class);
})
	->group('Fontes');

test('Deverá ser um array de fontes', function () {

	$fontes = new Fontes();

	expect($fontes->get())->toBeArray();
})
	->group('Fontes');