<?php

declare(strict_types=1);

namespace App\Application\Commands\Log;

use App\Application\Commands\Log\Enumerados\Level;
use App\Application\Commands\Log\DiscordWebhookToken;

interface Discord
{
    public function send(DiscordWebhookToken $channel, string $message): void;
    public function getChannel(Level $level): DiscordWebhookToken;
}