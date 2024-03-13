<?php

declare(strict_types=1);

namespace App\Infraestrutura\Adaptadores\Log;


class DiscordImplementation implements Discord
{

    public static $baseURL = 'https://discord.com/api/webhooks/';

    public DiscordChannel $channel;

    public function __construct(
        readonly private ClientHttp $_clientHTTP,
        readonly private Environment $env,
    ){

        $this->configDefault();

        $this->channel = new DiscordChannel(
            _env: $this->env
        );
    }
    
    public function getChannel(Level $level): DiscordWebhookToken
    {
        return $this->channel->get($level);
    }

    public function send(DiscordWebhookToken $channel, string $message): void
    {

        $this->configDefault();

        $this->_clientHTTP->post(
            endpoint: "{$channel->webhookID}/{$channel->webhookToken}", 
            data: [
                'content' => $message,
                //'username' => $this->businessEntity->tradeName->get(),
                //'avatar_url' => $this->_imobiliaria->logo->getUrl()
            ]
        );
    }

    private function configDefault(): void
    {

        $this->_clientHTTP->setConfig([
            'baseURL' => self::$baseURL,
            'headers' => [
                "Content-Type: application/x-www-form-urlencoded",
            ]
        ]);
    }
}