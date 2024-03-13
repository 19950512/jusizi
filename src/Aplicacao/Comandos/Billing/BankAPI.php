<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing;

use App\Application\Commands\Billing\Fronteiras\OutputBoundaryEmitirBoleto;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoleto;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryBoletosByFilters;
use App\Application\Commands\Billing\Fronteiras\OutputBoundaryConsultaBoletoByID;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryAlterarVencimentoBanco;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryAlterarJurosDiarioNoBanco;

interface BankAPI
{
    public function getAccessToken(): string;
    public function emitirBoleto(InputBoundaryEmitirBoleto $input): OutputBoundaryEmitirBoleto;
}