<?php

declare(strict_types=1);

namespace App\Application\Queries\Business;

use App\Application\Queries\Business\Fronteiras\InputBoundaryQuerieCreateAccount;
use App\Application\Queries\Business\Fronteiras\OutputGetColaboradores;

interface BusinessQueriesUsecase
{
    public function getAllColaboradores(): OutputGetColaboradores;

    public function createAccount(InputBoundaryQuerieCreateAccount $params): void;
}