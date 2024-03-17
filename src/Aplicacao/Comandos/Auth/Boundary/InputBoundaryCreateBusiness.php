<?php

declare(strict_types=1);

namespace App\Application\Commands\Autenticacao\Fronteiras;

final class InputBoundaryCreateBusiness
{
    public function __construct(
        readonly public string $name,
    ){}
}
