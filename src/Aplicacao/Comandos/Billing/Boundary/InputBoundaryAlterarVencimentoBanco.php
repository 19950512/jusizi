<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing\Fronteiras;

final class InputBoundaryAlterarVencimentoBanco
{
    public function __construct(
        public string $nossoNumero,
        public string $novaDataVencimento,
    ){}
}