<?php

declare(strict_types=1);

namespace App\Application\Queries\Client\Fronteiras;

final class OutputBoundaryClients
{
    private array $_clients = [];

    public function add(OutputClient $client): void
    {
        $this->_clients[] = $client;
    }

    public function get(): array
    {
        return $this->_clients;
    }
}