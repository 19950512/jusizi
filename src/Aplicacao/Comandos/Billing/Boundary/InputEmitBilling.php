<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing\Fronteiras;

final class InputEmitBilling
{

    public function __construct(
        readonly public string $contractID,
    )
    {}
}
