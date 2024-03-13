<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

final readonly class Mascara
{
    private string $mascara;
    function __construct(
        private string $value,
        private string $mask
    ){
        $value = preg_replace("/[^0-9]/", "", $this->value);
        $mask = '';
        $k = 0;
        for($i = 0; $i<=strlen($this->mask)-1; $i++) {
            if($this->mask[$i] == '#') {
                if(isset($value[$k])) $mask .= $value[$k++];
            } else {
                if(isset($mask[$i])) $mask .= $mask[$i];
            }
        }

        $this->mascara = $mask;
    }

    public function get(): string
    {
        return $this->mascara;
    }
}