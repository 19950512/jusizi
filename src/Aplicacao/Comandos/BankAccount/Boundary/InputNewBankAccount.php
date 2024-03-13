<?php

declare(strict_types=1);

namespace App\Application\Commands\BankAccount\Fronteiras;

final class InputNewBankAccount
{

    public function __construct(
        readonly public string $name,
        readonly public string $accountNumber,
        readonly public string $agenciaNumber,
        readonly public string $cedenteNumber,
        readonly public string $posto,
        readonly public string $cnab,
        readonly public string $bank,
    )
    {}
}
