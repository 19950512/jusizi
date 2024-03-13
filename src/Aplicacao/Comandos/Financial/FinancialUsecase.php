<?php

declare(strict_types=1);

namespace App\Application\Commands\Financial;

use App\Application\Commands\Financial\Fronteiras\InputBoundaryPost;

interface FinancialUsecase
{
    public function post(InputBoundaryPost $params): void;
}
