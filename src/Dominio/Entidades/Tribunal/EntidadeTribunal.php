<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Tribunal;

use App\Dominio\ObjetoValor\IdentificacaoUnica;

class EntidadeTribunal
{
    public function __construct(
        public IdentificacaoUnica $codigo,
        public string $codigoTribunal,
        public string $nome,
        public string $sigla,
    ){}
}