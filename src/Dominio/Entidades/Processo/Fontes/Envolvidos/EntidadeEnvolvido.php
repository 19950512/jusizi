<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Processo\Fontes;

use App\Dominio\ObjetoValor\IdentificacaoUnica;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Dominio\Entidades\Processo\Fontes\Envolvidos\Enumerados\Polo;
use App\Dominio\Entidades\Processo\Fontes\Envolvidos\Enumerados\Tipo;
use App\Dominio\Entidades\Processo\Fontes\Envolvidos\Enumerados\Natureza;

class EntidadeEnvolvido
{

    public function __construct(
        public IdentificacaoUnica $code,
        public NomeCompleto $nome,
        public int $quantidadeProcessos,
        public Natureza $tipoNatureza,
        public string $documento,
        public Tipo $tipo,
        public Polo $polo
    ){}
}