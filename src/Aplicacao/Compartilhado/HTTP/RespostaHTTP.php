<?php

declare(strict_types=1);

namespace App\Aplicacao\Compartilhado\HTTP;

final readonly class RespostaHTTP
{
    public function __construct(
        public int $code,
        public string | array $body,
    ){}
}