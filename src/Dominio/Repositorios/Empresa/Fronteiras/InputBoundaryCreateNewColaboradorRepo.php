<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Empresa\Fronteiras;

final class InputBoundaryCreateNewColaboradorRepo
{
    public function __construct(
        public string $code,
        public string $nome,
        public string $email,
        public string $businessID,
    ){}
}