<?php

declare(strict_types=1);

namespace App\Application\Commands\Auth;

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Application\Commands\Autenticacao\Fronteiras\InputBoundaryToken;
use App\Application\Commands\Autenticacao\Fronteiras\OutputBoundaryToken;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\OutputGetAccountByID;
use App\Application\Commands\Autenticacao\Fronteiras\InputBoundaryCreateAccount;
use App\Application\Commands\Autenticacao\Fronteiras\InputBoundaryCreateBusiness;

interface AuthUseCase
{
    public function token(InputBoundaryToken $params): OutputBoundaryToken;
    public function createAccount(InputBoundaryCreateAccount $params): void;
    public function createBusiness(InputBoundaryCreateBusiness $params): string;
    public function getAccountByID(string $id): OutputGetAccountByID;

    public function getAuthJTW(string $token, string $acc_id, string $businessID): string;
}