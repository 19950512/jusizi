<?php

declare(strict_types=1);

namespace App\Application\Queries\ChartOfAccount\Fronteiras;

final class OutputChartOfAccount
{
    public function __construct(
        public readonly string $id,
        public readonly string $nome,
    ){}
}