<?php

declare(strict_types=1);

namespace App\Application\Commands\Autenticacao\Fronteiras;

final class InputBoundaryCreateToken
{
    public function __construct(
        public string $token,
        public string $codigoContaBancaria,
        public string $bancoNome, 
        public string $expirationTime, 
        public string $expiresIn, 
        public string $tokenRefresh, 
        public string $tokenScope, 
        public string $tokenType
    ){}
}