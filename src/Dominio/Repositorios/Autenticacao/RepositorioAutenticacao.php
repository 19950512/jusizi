<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Auth;

use App\Dominio\Repositorios\Autenticacao\Fronteiras\InputCreateAccount;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\InputCreateBusiness;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\OutputBoundaryBusiness;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\OutputGetAccountByID;

interface RepositorioAutenticacao
{
    public function getAccountByID(string $id): OutputGetAccountByID;
    public function businessExistsByID(string $businessID): bool;
    public function getAccountByEmail(string $email): OutputGetAccountByID;
    public function createAccount(InputCreateAccount $params): void;
    public function saveNewBusiness(InputCreateBusiness $params): void;
    public function saveJWToken(string $token, string $accountID, string $businessID): void;
    public function getJWToken(string $token, string $accountID, string $businessID): string;
    public function getBusinessByID(string $businessID): OutputBoundaryBusiness;
    public function accountExistsByEmail(string $email): bool;
    public function accountExistsByEmailAndPassword(string $email, string $password): bool;
}