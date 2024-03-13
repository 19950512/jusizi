<?php

declare(strict_types=1);

namespace App\Application\Commands\Log;

use App\Application\Commands\Log\Enumerados\Level;

interface Log
{
    public function log(Level $level, string $message): void;
}