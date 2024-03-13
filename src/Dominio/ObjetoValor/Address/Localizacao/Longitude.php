<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor\Endereco\Localizacao;

final class Longitude
{
    public function __construct(
        private float $longitude
    )
    {

        if(!is_numeric($this->longitude)){
            throw new \InvalidArgumentException('Longitude is not valid');
        }

        if ($this->longitude < -180 || $this->longitude > 180) {
            throw new \InvalidArgumentException('Longitude is not valid');
        }
    }

    public function get(): float
    {
        return $this->longitude;
    }
}