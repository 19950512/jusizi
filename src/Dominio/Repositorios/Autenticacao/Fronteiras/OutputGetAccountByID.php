<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Autenticacao\Fronteiras;

final class OutputGetAccountByID
{

    public function __construct(
        readonly public string $id,
        readonly public string $nickname,
        readonly public string $email,
        readonly public string $businessID,
    ){}
}
