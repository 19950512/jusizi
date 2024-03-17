<?php

declare(strict_types=1);

namespace App\Application\Commands\Contract;

use App\Application\Commands\Contract\Fronteiras\InputBoundaryUpdateContract;
use App\Application\Commands\Contract\Fronteiras\InputBoundaryCreateNewContract;

interface ContractUseCase
{
    public function createNewContract(InputBoundaryCreateNewContract $params): void;
}