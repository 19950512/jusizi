<?php

use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraBuscarContaPorCodigo;

test('Deve ser uma instância de SaidaFronteiraBuscarContaPorCodigo', function(){
	$saidaFronteiraBuscarContaPorCodigo = new SaidaFronteiraBuscarContaPorCodigo(
		empresaCodigo: '0213801293709812730128',
		contaCodigo: '0213801293709812730128',
		nomeCompleto: 'João da Silva',
		email: 'email@email.com',
	);
	expect($saidaFronteiraBuscarContaPorCodigo)->toBeInstanceOf(SaidaFronteiraBuscarContaPorCodigo::class);
})
	->group('SaidaFronteiraBuscarContaPorCodigo');