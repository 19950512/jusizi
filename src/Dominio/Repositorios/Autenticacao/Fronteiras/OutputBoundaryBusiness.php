<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Autenticacao\Fronteiras;

final class OutputBoundaryBusiness
{
    public function __construct(
        readonly public string $businessID,
        readonly public string $name,
    ){}
}