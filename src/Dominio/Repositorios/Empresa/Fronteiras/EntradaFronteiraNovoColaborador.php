<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Empresa\Fronteiras;

final class EntradaFronteiraNovoColaborador
{
    public function __construct(
        public string $empresaCodigo,
        public string $colaboradorCodigo,
        public string $nomeCompleto,
        public string $email,
    ){}
}