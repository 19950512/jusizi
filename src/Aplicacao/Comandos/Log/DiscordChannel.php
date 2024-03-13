<?php

declare(strict_types=1);

namespace App\Application\Commands\Log;

use Exception;
use App\Shared\Environment;
use App\Application\Commands\Log\Enumerados\Level;

class DiscordChannel
{

    private static array $_channels = [];

    public function __construct(
        readonly private Environment $_env,
    ){
        self::$_channels = [
            Level::CRITICAL->name => new DiscordWebhookToken(
                webhookID: $this->_env->get('DISCORD_CRITICAL_WEBHOOK_ID'),
                webhookToken: $this->_env->get('DISCORD_CRITICAL_WEBHOOK_TOKEN')
            ),
            Level::ERROR->name => new DiscordWebhookToken(
                webhookID: $this->_env->get('DISCORD_ERROR_WEBHOOK_ID'),
                webhookToken: $this->_env->get('DISCORD_ERROR_WEBHOOK_ID')
            ),
            Level::INFO->name => new DiscordWebhookToken(
                webhookID: $this->_env->get('DISCORD_INFO_WEBHOOK_ID'),
                webhookToken: $this->_env->get('DISCORD_INFO_WEBHOOK_TOKEN')
            ),
        ];
    }

    static function get(Level $level): DiscordWebhookToken 
    {
        if(!isset(self::$_channels[$level->name])){
            throw new Exception("Discord channel not found");
        }

        return self::$_channels[$level->name];
    }
}
