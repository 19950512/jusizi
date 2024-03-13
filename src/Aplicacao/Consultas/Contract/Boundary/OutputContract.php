<?php

declare(strict_types=1);

namespace App\Application\Queries\Contract\Fronteiras;

final class OutputContract
{
    public function __construct(
        public readonly string $code,
        public readonly string $clientID,
        public readonly string $bankAccountID,
        public readonly float $value,
        public readonly int $dayEmitBilling,
    ){}
}