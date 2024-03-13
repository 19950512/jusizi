<?php

declare(strict_types=1);

namespace App\Application\Commands\Autenticacao\Fronteiras;

final class OutputBoundaryToken
{

    public function __construct(
        readonly public string $accessToken,
        readonly public int $expiresIn,
        readonly public string $tokenType,
    )
    {}
}
