<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing\Fronteiras;

final class InputPushtBilling
{
    public function __construct(
        readonly public string $chartOfAccountID,
        readonly public string $contractID,
        readonly public string $value,
        readonly public string $description,
    ){}
}
