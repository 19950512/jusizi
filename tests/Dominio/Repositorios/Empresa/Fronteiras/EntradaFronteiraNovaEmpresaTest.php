<?php

use App\Dominio\Repositorios\Autenticacao\Fronteiras\EntradaFronteiraNovaEmpresa;

test('Deve ser uma instância de EntradaFronteiraNovaEmpresa', function(){
	$entradaFronteiraNovaEmpresa = new EntradaFronteiraNovaEmpresa(
		empresaCodigo: 'iqwiofwqh0-123-=10',
		nome: 'Empresa Teste LTDA',
	);
	expect($entradaFronteiraNovaEmpresa)->toBeInstanceOf(EntradaFronteiraNovaEmpresa::class);
})
	->group('EntradaFronteiraNovaEmpresa');