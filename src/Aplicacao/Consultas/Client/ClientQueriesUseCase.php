<?php

declare(strict_types=1);

namespace App\Application\Queries\Client;

use App\Application\Queries\Client\Fronteiras\OutputBoundaryClients;

interface ClientQueriesUseCase
{
    public function getClients(): OutputBoundaryClients;
}