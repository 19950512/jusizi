<?php

declare(strict_types=1);

namespace App\Application\Queries\BankAccount\Fronteiras;

final class OutputBoundaryBankAccounts
{
    private array $_BankAccounts = [];

    public function add(OutputBankAccount $BankAccount): void
    {
        $this->_BankAccounts[] = $BankAccount;
    }

    public function get(): array
    {
        return $this->_BankAccounts;
    }
}