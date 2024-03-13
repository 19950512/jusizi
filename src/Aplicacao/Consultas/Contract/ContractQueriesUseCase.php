<?php

declare(strict_types=1);

namespace App\Application\Queries\Contract;

use App\Application\Queries\Contract\Fronteiras\OutputBoundaryContract;

interface ContractQueriesUseCase
{
    public function getContracts(): OutputBoundaryContract;
}