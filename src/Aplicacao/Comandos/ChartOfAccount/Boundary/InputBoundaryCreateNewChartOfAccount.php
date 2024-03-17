<?php

declare(strict_types=1);

namespace App\Application\Commands\ChartOfAccount\Fronteiras;

final class InputBoundaryCreateNewChartOfAccount
{

    public function __construct(
        public string $name,
        public string $description,
    ){}
}