<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

final class Descricao {
    
    function __construct(
        private readonly string $texto = ''
    ){}

    function get(): string {
        return $this->texto;
    }
}