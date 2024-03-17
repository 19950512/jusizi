<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Processo\Fontes;

use DateTime;
use App\Dominio\ObjetoValor\IdentificacaoUnica;
use App\Dominio\Entidades\Tribunal\EntidadeTribunal;
use App\Dominio\Entidades\Processo\Fontes\Enumerados\Grau;
use App\Dominio\Entidades\Processo\Fontes\Enumerados\Tipo;
use App\Dominio\Entidades\Processo\Fontes\Envolvidos\Envolvidos;

class EntidadeFonte
{

    public function __construct(
        public IdentificacaoUnica $code,
        public string $id,
        public string $descricao,
        public string $nome,
        public string $sigla,
        public Tipo             $tipo,
        public DateTime         $dataInicio,
        public DateTime         $dataUltimaMovimentacao,
        public bool             $segredoJustica,
        public bool             $arquivado,
        public bool             $fisico,
        public string           $sistema,
        public Grau             $grau,
        public EntidadeCapa       $capaEntity,
        public string           $url,
        public EntidadeTribunal $tribunalEntity,
        public int              $quantidadeMoimentacoes,
        public DateTime         $dataUltimaVerificacao,
        public Envolvidos       $envolvidos
    ){}
    
}