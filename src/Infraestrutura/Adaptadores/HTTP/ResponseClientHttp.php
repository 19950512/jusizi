<?php

declare(strict_types=1);

namespace App\Infraestrutura\Adaptadores\HTTP;

final class ResponseClientHttp 
{
    public function __construct(
        readonly public int $code,
        readonly public string | array $body,
    ){}
}