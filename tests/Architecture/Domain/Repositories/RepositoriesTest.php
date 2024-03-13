<?php

arch('Todas as "classes" de App\Dominio\Repositories devem ser interfaces exceto as Boundary.')
    ->expect('App\Dominio\Repositories')
    ->toBeInterfaces()
    ->ignoring([
        'App\Dominio\Repositorios\Autenticacao\Boundary',
        'App\Dominio\Repositorios\Business\Boundary',
    ]);

arch('Todas as "classes" de Domain\Repositories devem ter sufixo Repository.')
    ->expect('App\Dominio\Repositories')
    ->toHaveSuffix('Repository')
    ->ignoring([
        'App\Dominio\Repositorios\Autenticacao\Boundary',
        'App\Dominio\Repositorios\Business\Boundary',
    ]);