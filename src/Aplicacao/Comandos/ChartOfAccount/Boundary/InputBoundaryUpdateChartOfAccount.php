<?php

declare(strict_types=1);

namespace App\Application\Commands\ChartOfAccount\Fronteiras;

final class InputBoundaryUpdateChartOfAccount
{

    public function __construct(
        public string $id,
        public string $name,
        public string $description,
    ){}
}