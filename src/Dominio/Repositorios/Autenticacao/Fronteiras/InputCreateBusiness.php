<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Autenticacao\Fronteiras;

final class InputCreateBusiness
{
    public function __construct(
        public string $id,
        public string $name,
    ){}
}
