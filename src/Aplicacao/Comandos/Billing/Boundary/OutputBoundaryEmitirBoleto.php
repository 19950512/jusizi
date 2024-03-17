<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing\Fronteiras;

final class OutputBoundaryEmitirBoleto
{
    public function __construct(
        public string $seuNumero,
        public string $nossoNumero,
        public string $linhaDigitavel,
        public string $codigoBarras,
    ){}
}