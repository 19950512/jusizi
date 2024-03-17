<?php

declare(strict_types=1);

namespace App\Application\Queries\Client\Fronteiras;

final class OutputClient
{
    public function __construct(
        public readonly string $id,
        public readonly string $nome,
        public readonly string $telefone,
        public readonly string $email,
        public readonly string $documento,
    ){}
}