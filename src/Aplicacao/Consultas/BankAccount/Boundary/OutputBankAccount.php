<?php

declare(strict_types=1);

namespace App\Application\Queries\BankAccount\Fronteiras;

final class OutputBankAccount
{
    public function __construct(
        public readonly string $id,
        public readonly string $nome,
        public readonly string $conta,
        public readonly string $cedente,
        public readonly string $agencia,
        public readonly string $posto,
        public readonly string $cnab,
        public readonly string $banco,
    ){}
}