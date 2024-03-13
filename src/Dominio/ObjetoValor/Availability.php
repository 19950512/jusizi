<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

final class Availability
{
    public function __construct(
        private bool $availability
    ){}

    function get(): bool
    {
        return $this->availability;
    }
}