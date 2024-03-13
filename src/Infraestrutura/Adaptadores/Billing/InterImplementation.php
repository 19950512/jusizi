<?php

declare(strict_types=1);

namespace App\Infraestrutura\Adaptadores\Billing;

use App\Application\Commands\Billing\BankAPI;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoleto;
use App\Application\Commands\Billing\Fronteiras\OutputBoundaryEmitirBoleto;
use App\Application\Commands\Log\Enumerados\Level;
use App\Application\Commands\Log\Log;
use App\Aplicacao\Compartilhado\HTTP\ClientHttp;
use App\Dominio\Entidades\Financial\BankAccountEntity;
use App\Shared\Environment;
use Exception;

class InterImplementation implements BankAPI
{

    private static $baseURL = 'https://cdpj.partners.bancointer.com.br';

    public function __construct(
        readonly private Log $_log,
        readonly private Environment $env,
        readonly private BankAccountEntity $bankAccountEntity,
        readonly private ClientHttp $clientHttp,
    ){

        $certificado = [
            'key' => $this->bankAccountEntity->bankApi->certificateKey->get(),
            'pass' => $this->bankAccountEntity->bankApi->certificatePass->get(),
        ];

        $this->clientHttp->setConfig([
            'certificado' => $certificado,
            'baseURL' => self::$baseURL,
            'headers' => [
                "Content-Type: application/x-www-form-urlencoded",
                ]
            ]
        );
    }

    public function getAccessToken(): string
    {

        try {

            $certificado = [
                'key' => $this->bankAccountEntity->bankApi->certificateKey->get(),
                'pass' => $this->bankAccountEntity->bankApi->certificatePass->get(),
            ];

            $this->clientHttp->setConfig([
                'certificado' => $certificado,
                'baseURL' => self::$baseURL,
                'headers' => [
                    "Content-Type: application/x-www-form-urlencoded",
                    ]
                ]
            );

            $response = $this->clientHttp->post(
                '/oauth/v2/token',
                [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->bankAccountEntity->bankApi->credentialToken->get(),
                    'client_secret' => $this->bankAccountEntity->bankApi->credentialSecret->get(),
                    'scope' => 'boleto-cobranca.read boleto-cobranca.write pagamento-boleto.write pagamento-boleto.read'
                ]
            );

            if(is_array($response->body) and isset($response->body['access_token'])){
                return $response->body['access_token'];
            }

            if(str_contains($response->body, 'could not load PEM client certificate')){
                throw new Exception('Certificados não encontrados ou inválidos.');
            }
           
            $mensagemErro = $response->body['error_description'] ?? 'Algum erro ocorreu na comunicação com o banco.';
            throw new Exception($mensagemErro);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function emitirBoleto(InputBoundaryEmitirBoleto $input): OutputBoundaryEmitirBoleto
    {

        $return = new OutputBoundaryEmitirBoleto(
            seuNumero: $input->seuNumero,
            nossoNumero: '1234567890',
            linhaDigitavel: '1234567890123456789012345678906789012345678901234567890',
            codigoBarras: '12345678901234567890123456789012345678906789012345678901234567890',
        );

        $this->_log->log(Level::INFO, "Boleto emitido com sucesso. Seu número: {$input->seuNumero}, Nosso número: {$return->nossoNumero} Linha digitável: {$return->linhaDigitavel} Código de barras: {$return->codigoBarras}.");

        return $return;
    }
}