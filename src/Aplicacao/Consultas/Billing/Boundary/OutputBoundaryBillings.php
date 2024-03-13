<?php

declare(strict_types=1);

namespace App\Application\Queries\Billing\Fronteiras;

final class OutputBoundaryBillings
{
    private array $_billings = [];

    public function add(OutputBilling $Billing): void
    {
        $this->_billings[] = $Billing;
    }

    public function get(): array
    {
        return $this->_billings;
    }
}