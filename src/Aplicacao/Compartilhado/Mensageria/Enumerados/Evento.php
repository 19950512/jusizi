<?php

declare(strict_types=1);

namespace App\Aplicacao\Compartilhado\Mensageria\Enumerados;

enum Evento
{
    case EmitirNfse;
    case EmitirBoleto;
    case EnviarEmail;

    public function Filas(): Fila
    {
        return match ($this) {
            self::EmitirNfse => Fila::EMISSAO_NFSE_QUEUE,
            self::EmitirBoleto => Fila::EMISSAO_BOLETO_QUEUE,
            self::EnviarEmail => Fila::EMISSAO_EMAIL_QUEUE,
        };
    }
}