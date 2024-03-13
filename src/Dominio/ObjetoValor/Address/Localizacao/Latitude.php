<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor\Endereco\Localizacao;

use Exception;

final class Latitude
{
    public function __construct(
        private float $latitude
    )
    {

        if(!is_numeric($this->latitude)){
            throw new Exception('Latitude is not valid');
        }

        if ($this->latitude < -90 || $this->latitude > 90) {
            throw new Exception('Latitude is not valid');
        }
    }

    public function get(): float
    {
        return $this->latitude;
    }
}