<?php

use App\Dominio\ObjetoValor\IdentificacaoUnica;
use App\Infraestrutura\Adaptadores\HTTP\ImplementacaoCurlClienteHTTP;

beforeEach(function() {
	$this->clientHTTP = new ImplementacaoCurlClienteHTTP([
		'baseURL' => 'http://localhost:8052'
	]);
});

test('Deverá criar uma empresa', function() {

	$resposta = $this->clientHTTP->post('/empresa', [
		'nome' => 'Empresa Teste',
		'email' => ''
	]);

	expect($resposta->code)->toBe(200)
		->and($resposta->body)->toBeArray()
		->and($resposta->body['mensagem'])->toBe('Empresa criada com sucesso')
		->and($resposta->body['empresaID'])->toBeString()
		->and(new IdentificacaoUnica($resposta->body['empresaID']))->toBeInstanceOf(IdentificacaoUnica::class);
})
	->group('Integracao');