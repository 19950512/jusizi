<?php

declare(strict_types=1);

namespace App\Application\Queries\Business\Fronteiras;

use App\Dominio\Entidades\Business\EntidadeEmpresarial;

final class InputBoundaryQuerieCreateAccount
{
    public function __construct(
        readonly public EntidadeEmpresarial $business,
        readonly public string              $code,
        readonly public string              $name,
        readonly public string              $email,
        readonly public string              $phone,
    ){}

    public function toArray(): array
    {
        return [
            'business' => $this->business->toArray(),
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone
        ];
    }
}