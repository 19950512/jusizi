<?php

arch('Todas as "classes" de App\Dominio\Repositorios devem ser interfaces exceto as Fronteiras.')
    ->expect('App\Dominio\Repositories')
    ->toBeInterfaces()
    ->ignoring([
	    'App\Dominio\Repositorios\Autenticacao\Fronteiras',
	    'App\Dominio\Repositorios\Empresa\Fronteiras',
	    'App\Dominio\Repositorios\Token\Fronteiras',
    ]);

arch('Todas as "classes" de Dominio\Repositorios devem ter prefixo Repositorio.')
    ->expect('App\Dominio\Repositorios')
    ->toHavePrefix('Repositorio')
    ->ignoring([
        'App\Dominio\Repositorios\Autenticacao\Fronteiras',
        'App\Dominio\Repositorios\Empresa\Fronteiras',
	    'App\Dominio\Repositorios\Token\Fronteiras',
    ]);