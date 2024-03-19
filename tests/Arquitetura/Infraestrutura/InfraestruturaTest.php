<?php

arch('Infraestrutura não pode utilizar nada de nenhuma outra camada')
	->expect('App\Infraestrutura')
	->not->toBeUsedIn([
		'App\Dominio',
		'App\Configuracao',
		'App\Aplicacao'
	]);

arch('Infraestrutura não pode haver nenhuma entidade de dominio e nem da aplicacao')
	->expect('App\Infraestrutura')
	->not->toUse([
		'App\Dominio',
		'App\Configuracao',
		'App\Aplicacao'
	])
	->ignoring([
		'App\Dominio\Repositorios',
		'App\Aplicacao\Compartilhado'
	]);