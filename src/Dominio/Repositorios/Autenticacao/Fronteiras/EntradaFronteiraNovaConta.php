<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Autenticacao\Fronteiras;

final class EntradaFronteiraNovaConta
{
    public function __construct(
        public string $empresaCodigo,
        public string $contaCodigo,
        public string $nomeCompleto,
        public string $email,
        public string $senha,
    ){}
}
