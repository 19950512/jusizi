<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Empresa;

use App\Dominio\Repositorios\Empresa\Fronteiras\InputBoundaryCreateNewColaboradorRepo;

interface RepositorioEmpresa
{
    public function createNewColaborador(InputBoundaryCreateNewColaboradorRepo $params): void;
}