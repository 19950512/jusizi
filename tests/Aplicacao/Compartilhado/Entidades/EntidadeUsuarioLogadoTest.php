<?php

use App\Aplicacao\Compartilhado\Entidades\EntidadeUsuarioLogado;
use App\Dominio\ObjetoValor\Email;
use App\Dominio\ObjetoValor\IdentificacaoUnica;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraBuscarContaPorCodigo;

test('Deverá ser uma instância de EntidadeUsuarioLogado', function(){

	$params = new SaidaFronteiraBuscarContaPorCodigo(
		empresaCodigo: 'opjiqi0owfuioyqwlkmnqwe,mnqweoiqwi',
		contaCodigo: '1231236541298712589716512344988947',
		nomeCompleto: 'Ricardao Silva',
		email: 'email@para.teste'
	);

	$entidadeUsuarioLogado = EntidadeUsuarioLogado::instanciarEntidadeUsuarioLogado($params);

	expect($entidadeUsuarioLogado)->toBeInstanceOf(EntidadeUsuarioLogado::class);
})
	->group('EntidadeUsuarioLogado');

test('O EntidadeUsuarioLogado deve ser nome inválido', function(){

	$params = new SaidaFronteiraBuscarContaPorCodigo(
		empresaCodigo: 'opjiqi0owfuioyqwlkmnqwe,mnqweoiqwi',
		contaCodigo: '1231236541298712589716512344988947',
		nomeCompleto: 'Ricardao',
		email: 'email@para.teste'
	);

	$entidadeUsuarioLogado = EntidadeUsuarioLogado::instanciarEntidadeUsuarioLogado($params);
})
	->throws('Colaborador não possui nome completo válido. (Ricardao - Codigo: 1231236541298712589716512344988947) - Nome completo informado está inválido. (Ricardao)')
	->group('EntidadeUsuarioLogado');


test('O EntidadeUsuarioLogado deve ser e-mail inválido', function(){

	$params = new SaidaFronteiraBuscarContaPorCodigo(
		empresaCodigo: 'opjiqi0owfuioyqwlkmnqwe,mnqweoiqwi',
		contaCodigo: '1231236541298712589716512344988947',
		nomeCompleto: 'Ricardao Silva',
		email: 'emailpara.teste.com'
	);

	$entidadeUsuarioLogado = EntidadeUsuarioLogado::instanciarEntidadeUsuarioLogado($params);
})
	->throws('Colaborador não possui email válido. (emailpara.teste.com - Codigo: 1231236541298712589716512344988947) - O e-mail informado não é válido. (emailpara.teste.com)')
	->group('EntidadeUsuarioLogado');