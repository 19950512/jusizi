<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Processo\Fontes;

use DateTime;
use App\Dominio\ObjetoValor\Valor;

class EntidadeCapa
{
    public function __construct(
        public string $classe,
        public string $assunto,
        public string $assuntoNormalizado,
        public string $area,
        public string $orgaoJulgador,
        public Valor $causaValor,
        public string $causaMoeda,
        public DateTime $dataDistribuicao,
        public DateTime $dataArquivamento,
        public array $informacoesComplementares,
    ){}
}