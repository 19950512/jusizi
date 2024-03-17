<?php

declare(strict_types=1);

namespace App\Application\Commands\BankAccount;

use App\Application\Commands\BankAccount\Fronteiras\InputNewBankAccount;

interface BankAccountUseCase
{
    public function newBankAccount(InputNewBankAccount $params): void;
}