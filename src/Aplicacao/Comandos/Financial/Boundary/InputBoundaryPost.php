<?php

declare(strict_types=1);

namespace App\Application\Commands\Financial\Fronteiras;

final class InputBoundaryPost
{
    public function __construct(
        public string $clientID,
        public string $charofaccountID,
        public float $value,
        public string $description,
    ){}
}
