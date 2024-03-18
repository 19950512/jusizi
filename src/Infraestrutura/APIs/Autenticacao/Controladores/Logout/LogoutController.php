<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Autenticacao\Controladores\Logout;

use DI\Container;

final readonly class LogoutController
{

    public function __construct(
		private Container $container
    ){}

    public function index(): void
    {
    }
}

