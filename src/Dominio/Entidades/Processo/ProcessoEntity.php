<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Processo;

use DateTime;
use App\Dominio\ObjetoValor\CNJ;
use App\Dominio\ObjetoValor\IdentificacaoUnica;
use App\Dominio\Entidades\Processo\Fontes\Fontes;

class ProcessoEntity
{
    public function __construct(
        readonly public IdentificacaoUnica $codigo,
        public CNJ $cnj,
        public DateTime $dataInicio,
        public DateTime $dataUltimaMovimentacao,
        public int $quantidadeMovimentacoes,
        public DateTime $dataUltimaVerificacao,
        public Fontes $fontes,
    ){}
}