<?php

declare(strict_types=1);

use App\Aplicacao\Comandos\Autenticacao\Login\EmailESenha\ComandoLoginEmailESenha;

test('O e-mail precisa ser informado.', function(){

	$comandLoginEmailESenha = new ComandoLoginEmailESenha(
		email: '',
		senha: ''
	);

	$comandLoginEmailESenha->executar();
})
	->throws('O e-mail precisa ser informado adequadamente.')
	->group('ComandoLoginEmailESenha');

test('A senha precisa ser informada.', function(){

	$comandLoginEmailESenha = new ComandoLoginEmailESenha(
		email: 'email@teste.com',
		senha: ''
	);

	$comandLoginEmailESenha->executar();
})
	->throws('A senha precisa ser informada adequadamente.')
	->group('ComandoLoginEmailESenha');

test('A senha precisa ter no mínimo 9 caracteres', function(){

	$comandLoginEmailESenha = new ComandoLoginEmailESenha(
		email: 'email@teste.com',
		senha: '12345678'
	);

	$comandLoginEmailESenha->executar();
})
	->throws('A senha precisa ter no mínimo 9 caracteres.')
	->group('ComandoLoginEmailESenha');

test('A senha precisa ter no máximo 50 caracteres', function(){

	$comandLoginEmailESenha = new ComandoLoginEmailESenha(
		email: 'email@teste.com',
		senha: '123456789012345678901234567890123456789012345678901'
	);

	$comandLoginEmailESenha->executar();
})
	->throws('A senha atingiu o limite máximo de 50 caracteres.')
	->group('ComandoLoginEmailESenha');

