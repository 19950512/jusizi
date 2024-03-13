<?php

arch('Domain não pode utilizar nada de nenhuma outra camada')
    ->expect('App\Domain')
    ->not->toUse([
        'App\Infra',
        'App\Application',
        'App\Shared',
        'App\Config'
    ]);