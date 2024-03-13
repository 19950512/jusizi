<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use Exception;

final class TextoSimples
{
    public function __construct(
        private  string $string
    ){
        if(!self::validation($this->string)){
            throw new Exception('String Invalid.');
        }

        $this->string = trim($this->string);
        $this->string = strip_tags($this->string);
        $this->string = htmlspecialchars($this->string);
    }

    function get(): string{
        return $this->string;
    }

    static function validation(string $string): bool {
        return is_string($string);
    }
}