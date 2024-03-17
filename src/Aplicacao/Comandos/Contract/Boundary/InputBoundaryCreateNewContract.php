<?php

declare(strict_types=1);

namespace App\Application\Commands\Contract\Fronteiras;

final class InputBoundaryCreateNewContract
{
    public function __construct(
        public string $clientID,
        public string $contaBancariaID,
        public string $diaEmissaoCobranca,
        public string $valor,
    ){}
}