<?php

declare(strict_types=1);

namespace App\Aplicacao\Compartilhado\HTTP;

final class RespostaHTTP
{
    public function __construct(
        readonly public int $code,
        readonly public string | array $body,
    ){}
}