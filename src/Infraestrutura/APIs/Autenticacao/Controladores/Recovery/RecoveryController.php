<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Autenticacao\Controladores\Recovery;

use DI\Container;

final readonly class RecoveryController
{

    public function __construct(
		private Container $container
    ){}

    public function index(): void
    {
    }
}

