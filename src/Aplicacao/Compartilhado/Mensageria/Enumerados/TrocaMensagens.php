<?php

declare(strict_types=1);

namespace App\Aplicacao\Compartilhado\Mensageria\Enumerados;

/*
    case EMAIL_EXCHANGE = 'email_exchange';
    case EMISSAO_COBRANCA_EXCHANGE = 'emissao_cobranca_exchange';
*/

enum TrocaMensagens: string
{
    case EMISSAO_BOLETO_EXCHANGE = 'emissao_boleto_exchange';
    case EMISSAO_BOLETO_DLX_EXCHANGE = 'emissao_boleto_dlq_exchange';


    case EMISSAO_NFSE_EXCHANGE = 'emissao_nfse_exchange';
    case EMISSAO_NFSE_DLX_EXCHANGE = 'emissao_nfse_dlq_exchange';


    case EMISSAO_EMAIL_EXCHANGE = 'emissao_email_exchange';
    case EMISSAO_EMAIL_DLX_EXCHANGE = 'emissao_email_dlq_exchange';

    static public function getExchanges(): array
    {
        return [

            // EMISSAO BOLETO
            [
                'exchange' => self::EMISSAO_BOLETO_EXCHANGE,
                'type'=> 'direct',
            ],
            [
                'exchange' => self::EMISSAO_BOLETO_DLX_EXCHANGE,
                'type'=> 'fanout',
            ],

            
            // EMAIL
            [
                'exchange' => self::EMISSAO_EMAIL_EXCHANGE,
                'type'=> 'direct',
            ],
            [
                'exchange' => self::EMISSAO_EMAIL_DLX_EXCHANGE,
                'type'=> 'fanout',
            ],


            // EMISSAO NFSE
            [
                'exchange' => self::EMISSAO_NFSE_EXCHANGE,
                'type'=> 'direct',
            ],
            [
                'exchange' => self::EMISSAO_NFSE_DLX_EXCHANGE,
                'type'=> 'fanout',
            ],
        ];
    }
}