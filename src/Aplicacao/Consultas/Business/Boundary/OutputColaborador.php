<?php

declare(strict_types=1);

namespace App\Application\Queries\Business\Fronteiras;

final class OutputColaborador
{
    public function __construct(
        readonly public string $code,
        readonly public string $name,
        readonly public string $email,
        readonly public string $phone,
    ){}

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone
        ];
    }
}