<?php

declare(strict_types=1);

namespace App\Application\Queries\Billing\Fronteiras;

final class OutputBilling
{
    public function __construct(
        public readonly string $id,
        public readonly string $contractID,
        public readonly string $chartofaccountID,
        public readonly string $descricao,
        public readonly float $valor,
    ){}
}