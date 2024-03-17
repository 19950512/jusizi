<?php

declare(strict_types=1);

namespace App\Application\Commands\Client\Fronteiras;

final class InputUpdateClient
{

    public function __construct(
        readonly public string $id,
        readonly public string $name,
        readonly public string $email,
        readonly public string $phone,
        readonly public string $document, // CPF / CNPJ
    )
    {}
}
