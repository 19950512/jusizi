<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing\Fronteiras;

final class InputBoundaryEmitirBoletoBeneficiarioFinal
{
    public function __construct(
        public string $nome,
        public string $documento,
        public string $tipoPessoa,
        public string $cep,
        public string $endereco,
        public string $bairro,
        public string $cidade,
        public string $uf,
    ){}
}