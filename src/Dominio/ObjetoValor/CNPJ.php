<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use App\Dominio\ObjetoValor\DocumentoIdentificacao;
use Exception;

final class CNPJ implements DocumentoIdentificacao
{

    private string $number;
    function __construct(
        private string $document_number
    ){

        if(!self::valido($this->document_number)){
            throw new Exception('CNPJ is not valid');
        }

        $this->number = (new Mascara($this->document_number, '##.###.###/####-##'))->get();
    }

    function get(): string
    {
        return $this->number;
    }

    static function valido(string $document_number): bool
    {

        $cnpj = $document_number;

        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;
        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;
        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
}