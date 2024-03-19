<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Autenticacao\Fronteiras;

final readonly class SaidaFronteiraEmpresa
{
    public function __construct(
        public string $empresaCodigo,
        public string $nome,
	    public string $numeroDocumento
    ){}
}