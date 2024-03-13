<?php

declare(strict_types=1);

namespace App\Aplicacao\Compartilhado\Mensageria\Enumerados;

enum Fila: string
{
    case EMISSAO_BOLETO_QUEUE = 'emissao_boleto_queue';
    case EMISSAO_BOLETO_QUEUE_DLQ_QUEUE = 'emissao_boleto_queue_dlq_queue';

    case EMISSAO_NFSE_QUEUE = 'emissao_nfse_queue';
    case EMISSAO_NFSE_QUEUE_DLQ_QUEUE = 'emissao_nfse_queue_dlq_queue';

    case EMISSAO_EMAIL_QUEUE = 'emissao_email_queue';
    case EMISSAO_EMAIL_QUEUE_DLQ_QUEUE = 'emissao_email_queue_dlq_queue';

    static public function Ligacoes(): array
    {
        return [
            // NFSE
            [
                'queue' => self::EMISSAO_NFSE_QUEUE,
                'exchange' => Exchange::EMISSAO_NFSE_EXCHANGE,
            ],
            [
                'queue' => self::EMISSAO_NFSE_QUEUE_DLQ_QUEUE,
                'exchange' => Exchange::EMISSAO_NFSE_DLX_EXCHANGE,
            ],

            // EMAIL
            [
                'queue' => self::EMISSAO_EMAIL_QUEUE,
                'exchange' => Exchange::EMISSAO_EMAIL_EXCHANGE,
            ],
            [
                'queue' => self::EMISSAO_EMAIL_QUEUE_DLQ_QUEUE,
                'exchange' => Exchange::EMISSAO_EMAIL_DLX_EXCHANGE,
            ],


            // BOLETO
            [
                'queue' => self::EMISSAO_BOLETO_QUEUE,
                'exchange' => Exchange::EMISSAO_BOLETO_EXCHANGE,
            ],
            [
                'queue' => self::EMISSAO_BOLETO_QUEUE_DLQ_QUEUE,
                'exchange' => Exchange::EMISSAO_BOLETO_DLX_EXCHANGE,
            ],
        ];
    }

    static public function Filas(): array
    {
        return [

            // NFSE
            [
                'queue' => Fila::EMISSAO_NFSE_QUEUE,
                'dlx' => Exchange::EMISSAO_NFSE_DLX_EXCHANGE,
            ],
            [
                'queue' => Fila::EMISSAO_NFSE_QUEUE_DLQ_QUEUE,
                'dlx' => Exchange::EMISSAO_NFSE_DLX_EXCHANGE,
            ],


            // BOLETO
            [
                'queue' => Fila::EMISSAO_BOLETO_QUEUE,
                'dlx' => Exchange::EMISSAO_BOLETO_DLX_EXCHANGE,
            ],
            [
                'queue' => Fila::EMISSAO_BOLETO_QUEUE_DLQ_QUEUE,
                'dlx' => Exchange::EMISSAO_BOLETO_DLX_EXCHANGE,
            ],


            // EMAIL
            [
                'queue' => Fila::EMISSAO_EMAIL_QUEUE,
                'dlx' => Exchange::EMISSAO_EMAIL_DLX_EXCHANGE,
            ],
            [
                'queue' => Fila::EMISSAO_EMAIL_QUEUE_DLQ_QUEUE,
                'dlx' => Exchange::EMISSAO_EMAIL_DLX_EXCHANGE,
            ],
        ];
    }
}