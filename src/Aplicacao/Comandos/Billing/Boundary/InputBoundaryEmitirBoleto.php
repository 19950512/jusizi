<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing\Fronteiras;

use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoletoPagador;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoletoBeneficiarioFinal;

final class InputBoundaryEmitirBoleto
{
    public function __construct(
        public string $banco,
        public string $businessID,
        public string $bankAccountID,
        public InputBoundaryEmitirBoletoPagador $pagador,
        public InputBoundaryEmitirBoletoBeneficiarioFinal $beneficiarioFinal,
        public string $seuNumero,
        public string $valor,
        public string $dataVencimento,
        public string $mensagem,
        public string $multa,
        public string $juros,
        public string $valorDescontoAntecipacao,
        public string $composicaoBoletoTexto,
        public string $tipoDesconto,
        public string $tipoJuros,
        public string $tipoMulta,
    ){}
}