<?php

declare(strict_types=1);

namespace App\Application\Commands\Business;

use App\Application\Commands\Business\Fronteiras\InputBoundaryCreateNewColaborador;

interface BusinessUsecase
{
    public function createNewColaborador(InputBoundaryCreateNewColaborador $params): void;
}
