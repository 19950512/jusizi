<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use Exception;

final class Telefone
{
    function __construct(
        private string $numero
    ){
        if(!$this->_validation($this->numero)){
            throw new Exception('O número do telefone informado ("'.$this->numero.'") não é válido.');
        }

        $this->numero = (new Mascara(
            value: $this->numero,
            mask: '(##) #####-####'
        ))->get();
    }

    private function _validation(string $number): bool
    {
        return !!preg_match("/^\((\d{2})?\)?|(\d{2})? ?9\d{4}-?\d{4}$/i", $number);
    }

    function get(): string
    {
        return $this->numero;
    }
}