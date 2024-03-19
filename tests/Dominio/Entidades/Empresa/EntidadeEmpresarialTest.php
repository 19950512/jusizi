<?php


use App\Dominio\Entidades\Empresa\EntidadeEmpresarial;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraEmpresa;

test('Pode instanciar EntidadeEmpresarial com informações válidas.', function(){
    $params = new SaidaFronteiraEmpresa(
		empresaCodigo: 'ccacc179-05e4-4f3a-8b81-69096370b8ca',
	    nome: 'Teste Empresa',
		numeroDocumento: '03623589000172'
    );

    $entidade = EntidadeEmpresarial::instanciarEntidadeEmpresarial($params);

    expect($entidade)->toBeInstanceOf(EntidadeEmpresarial::class)
	    ->and($entidade->codigo->get())->toBe('ccacc179-05e4-4f3a-8b81-69096370b8ca')
	    ->and($entidade->nomeCompleto->get())->toBe('Teste Empresa');
})
	->group('EntidadeEmpresarial');

test('Lança Exception quando tenta instanciar uma Empresa com nome inválido', function(){
    $params = new SaidaFronteiraEmpresa(
		empresaCodigo: 'ccacc179-05e4-4f3a-8b81-69096370b8ca',
	    nome: '',
		numeroDocumento: '03623589000172'
    );

    EntidadeEmpresarial::instanciarEntidadeEmpresarial($params);
})
	->throws('Nome completo informado está inválido. ()')
	->group('EntidadeEmpresarial');

test('Lança Exception quando tenta instanciar uma Empresa com código inválido', function(){
	$params = new SaidaFronteiraEmpresa(
		empresaCodigo: '1234567',
	    nome: 'Teste Empresa',
		numeroDocumento: '03623589000172'
	);

	$entidadeEmpresarial = EntidadeEmpresarial::instanciarEntidadeEmpresarial($params);
})
	->throws('O código informado está inválido. (1234567)')
	->group('EntidadeEmpresarial');

test('Lança Exception quando tenta instanciar uma Empresa com nome inválido com 1 palavra', function(){
	 $params = new SaidaFronteiraEmpresa(
		empresaCodigo: 'ccacc179-05e4-4f3a-8b81-69096370b8ca',
	    nome: 'Empresa',
		numeroDocumento: '03623589000172'
    );

	$entidadeEmpresarial = EntidadeEmpresarial::instanciarEntidadeEmpresarial($params);
})
	->throws("O nome completo da Entidade Empresarial 'Empresa' ID: ccacc179-05e4-4f3a-8b81-69096370b8ca não está válido. Nome completo informado está inválido.")
	->group('EntidadeEmpresarial');

test('Devera retornar um array com todas as informacoes da EntidadeEmpresa', function(){
	$params = new SaidaFronteiraEmpresa(
		empresaCodigo: 'ccacc179-05e4-4f3a-8b81-69096370b8ca',
	    nome: 'Teste Empresa',
		numeroDocumento: '03623589000172'
	);

	$entidadeEmpresarial = EntidadeEmpresarial::instanciarEntidadeEmpresarial($params);

	$informacoes = $entidadeEmpresarial->toArray();

	expect($informacoes)->toBeArray()
		->and($informacoes['codigo'])->toBe('ccacc179-05e4-4f3a-8b81-69096370b8ca')
		->and($informacoes['nomeFantasia'])->toBe('Teste Empresa')
		->and($informacoes['documentoTipo'])->toBe('CNPJ')
		->and($informacoes['documentoNumero'])->toBe('03.623.589/0001-72')
		->and(count($informacoes))->toBe(4);

})
	->group('EntidadeEmpresarial');