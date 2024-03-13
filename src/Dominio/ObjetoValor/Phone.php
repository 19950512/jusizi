<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use Exception;

final class Phone
{
    function __construct(
        private string $number
    ){
        if(!$this->_validation($this->number)){
            throw new Exception('Phone is not valid');
        }

        $this->number = (new Mascara(
            value: $this->number,
            mask: '(##) #####-####'
        ))->get();
    }

    private function _validation(string $number): bool {
        return !!preg_match("/^\((\d{2})?\)?|(\d{2})? ?9\d{4}-?\d{4}$/i", $number);
    }

    function get(): string {
        return $this->number;
    }
}