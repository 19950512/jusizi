<?php

$blackListFunctions = [
    'dd',
    'dump',
    'var_dump',
    'print_r',
    'exit'
];

arch('Não podem haver '. implode(', ', $blackListFunctions) .' no código')
    ->expect($blackListFunctions)
    ->not->toBeUsed();

arch('Todas as classes devem utilizar StrictTypes')
    ->expect('App')
    ->toUseStrictTypes();