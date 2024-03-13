<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing\Fronteiras;

final class InputBoundaryEmitirBoletoPagador
{
    public function __construct(
        public string $documento,
        public string $tipoPessoa,
        public string $nome,
        public string $endereco,
        public string $cidade,
        public string $uf,
        public string $cep,
        public string $telefone = '',
        public string $email = '',
        public string $enderecoNumero = '',
        public string $enderecoComplemento = '',
    ){}
}