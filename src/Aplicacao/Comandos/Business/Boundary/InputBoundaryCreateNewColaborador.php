<?php

declare(strict_types=1);

namespace App\Application\Commands\Business\Fronteiras;

use App\Application\Commands\Share\IdentificacaoUnica;

final class InputBoundaryCreateNewColaborador
{
    public function __construct(
        public string $nome,
        public string $email
    ){}
}
