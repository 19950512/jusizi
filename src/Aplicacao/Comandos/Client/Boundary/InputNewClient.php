<?php

declare(strict_types=1);

namespace App\Application\Commands\Client\Fronteiras;

final class InputNewClient
{

    public function __construct(
        readonly public string $name,
        readonly public string $email,
        readonly public string $document, // CPF / CNPJ
        readonly public string $phone,
    )
    {}
}
