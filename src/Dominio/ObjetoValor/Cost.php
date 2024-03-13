<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use Exception;

final class Cost {

    public function __construct(
        private float $cost
    ){

        if(!self::validation($this->cost)){
            throw new Exception('Cost Invalid.');
        }
    }

    static function validation(float $cost): bool {
        return $cost >= 0;
    }

    function get(): float{
        return $this->cost;
    }
}