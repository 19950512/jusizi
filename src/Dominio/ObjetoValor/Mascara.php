<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

final readonly class Mascara
{
    private string $mask;
    function __construct(
        private string $texto,
        private string $mascara
    ){
        $value = preg_replace("/[^0-9]/", "", $this->texto);
        $mask = '';
        $k = 0;
        for($i = 0; $i<=strlen($this->mascara)-1; $i++) {
            if($this->mascara[$i] == '#') {
                if(isset($value[$k])) $mask .= $value[$k++];
            } else {
                if(isset($mask[$i])) $mask .= $mask[$i];
            }
        }

        $this->mask = $mask;
    }

    public function get(): string
    {
        return $this->mask;
    }
}