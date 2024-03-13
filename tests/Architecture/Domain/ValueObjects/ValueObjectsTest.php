<?php

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Dominio\ObjetoValor\CNPJ;
use App\Dominio\ObjetoValor\CPF;
use App\Dominio\ObjetoValor\DocumentoIdentificacao;
use App\Infra\Api\Controllers\Middlewares\Authorization;

arch('ValueObjects não pode utilizar nenhuma classe de nenhuma camada.')
    ->expect('App\Dominio\ValueObjects')
    ->toUseNothing();

arch('ValueObjects não podem extender nem implementar nada.')
    ->expect('App\Dominio\ValueObjects')
    ->toExtendNothing()
    ->toImplementNothing()
    ->ignoring([
        CPF::class,
        CNPJ::class
    ]);

arch('ValueObjects precisam necessáriamente ser final')
    ->expect('App\Dominio\ValueObjects')
    ->toBeFinal()
    ->ignoring([
        TypeRegisterInterface::class
    ]);

arch('ValueObjects não podem extender nada')
    ->expect('App\Dominio\ValueObjects')
    ->toExtendNothing();

arch('ValueObjects possuem o método get')
    ->expect('App\Dominio\ValueObjects')
    ->toHaveMethod('get');

arch('ValueObjects possuem construtor publico')
    ->expect('App\Dominio\ValueObjects')
    ->toHaveConstructor()
    ->ignoring([
        TypeRegisterInterface::class
    ]);

arch('ValueObjects podem ser utilizados somente em App\Domain ou App\Application exceto Authorization (Middleware).')
    ->expect('App\Dominio\ValueObjects')
    ->toOnlyBeUsedIn([
        'App\Domain',
        'App\Application'
    ])
    ->ignoring([
        Authorization::class
    ]);