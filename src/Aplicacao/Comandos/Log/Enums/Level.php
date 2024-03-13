<?php

declare(strict_types=1);

namespace App\Application\Commands\Log\Enumerados;

enum Level
{
    case DEBUG;
    case INFO;
    case WARNING;
    case ERROR;
    case CRITICAL;
}