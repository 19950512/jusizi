<?php

declare(strict_types=1);

namespace App\Application\Queries\Contract\Fronteiras;

use App\Application\Queries\Contract\Fronteiras\OutputContract;

final class OutputBoundaryContract
{
    private array $_contracts = [];

    public function add(OutputContract $Contract): void
    {
        $this->_contracts[] = $Contract;
    }

    public function get(): array
    {
        return $this->_contracts;
    }
}