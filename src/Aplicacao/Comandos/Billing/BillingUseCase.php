<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing;

use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitidoComSucesso;
use App\Dominio\Entidades\Contract\ContractEntity;
use App\Application\Commands\Billing\Fronteiras\InputPushtBilling;
use App\Dominio\Entidades\OrderService\OrderServiceEntity;

interface BillingUseCase
{
    public function pushBilling(InputPushtBilling $params): void;
    public function emitBillingToday(): void;
    public function registerBilling(ContractEntity $contractEntity): void;
    public function registerBillingOrderService(OrderServiceEntity $orderServiceEntity): void;
    public function boletoEmitidoComSucesso(InputBoundaryEmitidoComSucesso $params): void;
}