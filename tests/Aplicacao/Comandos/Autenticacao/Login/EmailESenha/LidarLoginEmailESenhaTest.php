<?php

declare(strict_types=1);

use App\Aplicacao\Comandos\Autenticacao\Login\EmailESenha\ComandoLoginEmailESenha;
use App\Aplicacao\Comandos\Autenticacao\Login\EmailESenha\LidarLoginEmailESenha;
use App\Aplicacao\Comandos\Comando;

test('LidarLoginEmailESenha só pode lidar com ComandoLoginEmailESenha.', function(){

	$comandoFake = Mockery::mock(Comando::class)
		->shouldReceive('obterEmail')
		->andReturn('')
		->getMock();

	$comandoLoginEmailESenha = new LidarLoginEmailESenha();

	$comandoLoginEmailESenha->lidar($comandoFake);

})
	->throws('Ops, não sei lidar com esse comando.')
	->group('LidarLoginEmailESenha');

test('LidarLoginEmailESenha deve lidar com ComandoLoginEmailESenha com sucesso.', function(){

	$comandoLoginEmailESenha = new ComandoLoginEmailESenha(
		email: 'matheus@email.com',
		senha: '123456789'
	);

	$comandoLoginEmailESenha->executar();

	$lidarLoginEmailESenha = new LidarLoginEmailESenha();

	expect($lidarLoginEmailESenha->lidar($comandoLoginEmailESenha))->toBeNull();
})
	->group('LidarLoginEmailESenha');