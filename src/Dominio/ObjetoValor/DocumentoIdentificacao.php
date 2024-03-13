<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

interface DocumentoIdentificacao
{
    static function valido(string $document_number): bool;
    function get(): string;
}