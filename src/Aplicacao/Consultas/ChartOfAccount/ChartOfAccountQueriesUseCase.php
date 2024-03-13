<?php

declare(strict_types=1);

namespace App\Application\Queries\ChartOfAccount;

use App\Application\Queries\ChartOfAccount\Fronteiras\OutputBoundaryChartOfAccount;

interface ChartOfAccountQueriesUseCase
{
    public function getChartOfAccounts(): OutputBoundaryChartOfAccount;
}