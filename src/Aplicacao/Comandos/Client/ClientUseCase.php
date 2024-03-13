<?php

declare(strict_types=1);

namespace App\Application\Commands\Client;

use App\Application\Commands\Client\Fronteiras\InputNewClient;
use App\Application\Commands\Client\Fronteiras\InputUpdateClient;

interface ClientUseCase
{
    public function newClient(InputNewClient $params): void;

    public function updateClient(InputUpdateClient $params): void;

    public function deleteClient(string $id): void;
}