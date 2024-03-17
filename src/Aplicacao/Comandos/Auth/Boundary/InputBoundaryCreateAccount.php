<?php

declare(strict_types=1);

namespace App\Application\Commands\Autenticacao\Fronteiras;

final class InputBoundaryCreateAccount
{

    public function __construct(
        readonly public string $businessID,
        readonly public string $email,
        readonly public string $password,
        readonly public string $nickname,
    )
    {}
}
