<?php

// Apelido::class

use App\Dominio\ObjetoValor\Apelido;

test('Deve ser uma instancia de Apelido', function () {

	$apelido = new Apelido('Apelido');

	expect($apelido)->toBeInstanceOf(Apelido::class);
})
	->group('Apelido');

test('Deve ser um Apelido com "Apelido"', function () {

	$apelido = new Apelido('Apelido');

	expect($apelido->get())->toEqual('Apelido');
})
	->group('Apelido');

test('Deve ser um Apelido com "Apelido Legal"', function () {

	$apelido = new Apelido('apelido legal');

	expect($apelido->get())->toEqual('Apelido Legal');
})
	->group('Apelido');

test('Deve retornar um erro, apelido inválido', function () {

	$apelido = new Apelido('!!apelido legal!!');
})
	->throws('Apelido informado está inválido. (!!apelido Legal!!)')
	->group('Apelido');