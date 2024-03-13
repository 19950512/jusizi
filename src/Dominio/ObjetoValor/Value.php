<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use Exception;

final class Value {

    public function __construct(
        private readonly float $value
    ){

        if(!self::validation($this->value)){
            throw new Exception('Value Invalid.');
        }
    }

    static function validation(float $value): bool {
        return is_numeric($value);
    }

    function get(): float{
        return $this->value;
    }
}