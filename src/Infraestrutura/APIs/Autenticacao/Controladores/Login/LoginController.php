<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Autenticacao\Controladores\Login;

use DI\Container;

final readonly class LoginController
{

    public function __construct(
		private Container $container
    ){}

    public function index(): void
    {
    }
}

