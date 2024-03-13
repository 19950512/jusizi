<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Autenticacao\Fronteiras;

final class InputCreateAccount
{
    public function __construct(
        public string $businessID,
        public string $id,
        public string $nickname,
        public string $email,
        public string $password,
    ){}
}
