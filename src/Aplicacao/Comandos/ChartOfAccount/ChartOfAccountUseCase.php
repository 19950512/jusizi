<?php

declare(strict_types=1);

namespace App\Application\Commands\ChartOfAccount;

use App\Application\Commands\ChartOfAccount\Fronteiras\InputBoundaryUpdateChartOfAccount;
use App\Application\Commands\ChartOfAccount\Fronteiras\InputBoundaryCreateNewChartOfAccount;
use App\Dominio\Repositorios\ChartOfAccount\Fronteiras\OutputCOAGetByID;

interface ChartOfAccountUseCase
{
    public function createNewChartOfAccount(InputBoundaryCreateNewChartOfAccount $params): void;
    public function updateChartOfAccount(InputBoundaryUpdateChartOfAccount $params): void;
    public function checkIfCharOfAccountExistsByName(string $name): bool;
    public function getCharOfAccountByName(string $name): OutputCOAGetByID;
    public function getAllChartOfAccounts(): array;
    
}