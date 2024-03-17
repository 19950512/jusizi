<?php

declare(strict_types=1);

namespace App\Application\Queries\Financial;

use App\Application\Commands\Financial\Fronteiras\InputBoundaryPost;
use App\Dominio\Repositorios\Financial\Fronteiras\InputBoundaryPostRepository;

interface FinancialQuery
{
    public function getSaldoClient(string $clientID): float;

    public function post(InputBoundaryPostRepository $params): void;

    public function saveSaldoAtual(string $clientID, float $saldoAtual): void;
}