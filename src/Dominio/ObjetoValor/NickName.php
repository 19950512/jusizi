<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use Exception;

final class NickName {

    function __construct(
        public string $name
    ){

        $this->name = mb_convert_case($this->name, MB_CASE_LOWER, 'UTF-8');

        $this->name = ucwords($this->name);

        $mustache = [
            ' Da ' => ' da ',
            ' De ' => ' de ',
            ' Di ' => ' di ',
            ' Do ' => ' do ',
            ' Du ' => ' du ',
        ];

        $this->name = str_replace(array_keys($mustache), array_values($mustache), $this->name);

        if(!self::validation($this->name)){
            throw new Exception("Full name is not valid");
        }
    }

    static function validation(string $name): bool {
        
        $name = str_replace('  ', ' ', $name);

        if(!is_string($name)){
            return false;
        }

        return true;
    }

    public function get(){
        return $this->name;
    }
}