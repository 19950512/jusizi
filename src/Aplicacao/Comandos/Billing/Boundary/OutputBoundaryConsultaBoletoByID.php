<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing\Fronteiras;

final class OutputBoundaryConsultaBoletoByID
{
    public function __construct(
        public string $linhaDigitavel,
        public string $codigoBarras,
        public string $seuNumero,
        public string $nossoNumero,
        public string $dataEmissao,
        public string $dataVencimento,
        public string $situacao,
        public string $valor,
        public string $valorPago,
        public string $multa,
        public string $juros,
        public string $pagador = '',
        public string $dataPagamento = ''
    ){}
}