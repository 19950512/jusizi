<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use Exception;

final class NomeCompleto {

    function __construct(
        private string $nome
    ){

        $this->nome = mb_convert_case($this->nome, MB_CASE_LOWER, 'UTF-8');

        $this->nome = ucwords($this->nome);

        $mustache = [
            ' Da ' => ' da ',
            ' De ' => ' de ',
            ' Di ' => ' di ',
            ' Do ' => ' do ',
            ' Du ' => ' du ',
        ];

        $this->nome = str_replace(array_keys($mustache), array_values($mustache), $this->nome);

        if(!self::validation($this->nome)){
            throw new Exception("Nome completo informado está inválido. ({$this->nome})");
        }
    }

    static function validation(string $name): bool
    {

        $name = str_replace('  ', ' ', $name);

        $contain_words_only = preg_match("/^[A-ZÀ-Ÿ][A-zÀ-ÿ']+\s([A-zÀ-ÿ']\s?)*[A-ZÀ-Ÿ][A-zÀ-ÿ']+$/", trim($name));
        return !!$contain_words_only;
    }

    public function get(): string
    {
        return $this->nome;
    }
}