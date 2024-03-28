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
		'nome_fantasia' => 'Empresa Teste',
		'responsavel_nome_completo' => 'Matheus Maydana',
		'responsavel_email' => 'mattmaydana@gmail.com',
		'responsavel_senha' => '89578779Aa!'
	]);

	dd($resposta);

	expect($resposta->code)->toBe(201)
		->and($resposta->body)->toBeArray()
		->and($resposta->body['mensagem'])->toBe('Empresa criada com sucesso')
		->and($resposta->body['empresaID'])->toBeString()
		->and(new IdentificacaoUnica($resposta->body['empresaID']))->toBeInstanceOf(IdentificacaoUnica::class);
})
	->group('Integracao');