<?php

declare(strict_types=1);

namespace App\Application\Commands\Log;

final class DiscordWebhookToken
{
    public function __construct(
        readonly public string $webhookID,
        readonly public string $webhookToken
    ){}
}