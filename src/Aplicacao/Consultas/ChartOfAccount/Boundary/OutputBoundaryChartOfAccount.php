<?php

declare(strict_types=1);

namespace App\Application\Queries\ChartOfAccount\Fronteiras;

final class OutputBoundaryChartOfAccount
{
    private array $_chartOfAccount = [];

    public function add(OutputChartOfAccount $client): void
    {
        $this->_chartOfAccount[] = $client;
    }

    public function get(): array
    {
        return $this->_chartOfAccount;
    }
}