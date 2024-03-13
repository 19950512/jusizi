<?php

declare(strict_types=1);

namespace App\Application\Commands\Autenticacao\Fronteiras;

final class InputBoundaryToken
{

    public function __construct(
        readonly public string $email,
        readonly public string $password,
    )
    {}
}
