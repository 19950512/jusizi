<?php

declare(strict_types=1);

namespace App\Application\Queries\Billing;

use App\Application\Queries\Billing\Fronteiras\OutputBoundaryBillings;

interface BillingQueriesUseCase
{
    public function getBillings(): OutputBoundaryBillings;
}