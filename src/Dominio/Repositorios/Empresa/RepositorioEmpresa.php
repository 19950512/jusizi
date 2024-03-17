<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Empresa;

use App\Dominio\Repositorios\Empresa\Fronteiras\EntradaFronteiraNovoColaborador;

interface RepositorioEmpresa
{
    public function novoColaborador(EntradaFronteiraNovoColaborador $params): void;
}