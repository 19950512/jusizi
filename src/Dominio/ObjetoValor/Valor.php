<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use Exception;

final class Valor {

    public function __construct(
        private readonly float $valor
    ){

        if(!self::validation($this->valor)){
            throw new Exception("O valor informado não é válido. (".$this->valor.")");
        }
    }

    static function validation(float $valor): bool
    {
        return is_numeric($valor);
    }

    function get(): float
    {
        return $this->valor;
    }
}