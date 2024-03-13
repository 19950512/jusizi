<?php

declare(strict_types=1);

namespace App\Application\Queries\Business\Fronteiras;

final class OutputGetColaboradores
{

    public array $colaboradores = [];

    public function add(OutputColaborador $colaborador): void
    {
        $this->colaboradores[] = $colaborador;
    }

    public function toArray(): array
    {
        return array_map(function($colaborador){
            return $colaborador->toArray();
        }, $this->colaboradores);
    }
}