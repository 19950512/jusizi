<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

final class IdentificacaoUnica {

    function __construct(
        private string $data = ''
    ){

        if(strlen($this->data) == 36){
            $this->data = $this->data;
            return;
        }

        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $this->data = !empty($this->data) ? $this->data : random_bytes(16);
        assert(strlen($this->data) == 16);
    
        // Set version to 0100
        $this->data[6] = chr(ord($this->data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $this->data[8] = chr(ord($this->data[8]) & 0x3f | 0x80);
    
        // Output the 36 character UUID.
        $this->data = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($this->data), 4));
    }

    function get(): string {
        return $this->data;
    }
}