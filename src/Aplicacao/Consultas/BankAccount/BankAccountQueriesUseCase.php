<?php

declare(strict_types=1);

namespace App\Application\Queries\BankAccount;

use App\Application\Queries\BankAccount\Fronteiras\OutputBoundaryBankAccounts;

interface BankAccountQueriesUseCase
{
    public function getBankAccounts(): OutputBoundaryBankAccounts;
}