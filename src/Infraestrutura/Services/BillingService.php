#!/usr/bin/php
<?php

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__.'/../../../vendor/autoload.php';

use App\Application\Commands\Share\IdentificacaoUnica;
use App\Aplicacao\Compartilhado\HTTP\ClientHttp;
use App\Configuracao\Containerapp;
use App\Dominio\Entidades\Business\EntidadeUsuarioLogado;
use App\Dominio\Entidades\Financial\BankAccountEntity;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraBuscarContaPorCodigo;
use App\Dominio\Repositorios\BankAccount\BankAccountRepository;
use App\Dominio\Repositorios\BankAccount\Fronteiras\OutputGetBankAccountByID;
use App\Infraestrutura\Adaptadores\Billing\InterImplementation;
use App\Infra\EventBus\Event;
use App\Shared\Environment;
use App\Shared\EventBus\EventBus;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Billing\BankAPI;
use App\Application\Commands\Log\Enumerados\Level;
use App\Application\Commands\Billing\BillingUseCase;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoleto;
use App\Application\Commands\Billing\Fronteiras\OutputBoundaryEmitirBoleto;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitidoComSucesso;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoletoPagador;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoletoBeneficiarioFinal;

$containerApp = Containerapp::getInstance();

$container = $containerApp->get([
    'EVENT_BUS_HOST' => 'localhost',
    'DB_HOST' => 'localhost',
    'DB_PORT' => '8050'
]);

$eventBus = $container->get(EventBus::class);
$container->set('businessID', new IdentificacaoUnica('1dd966da-4228-474a-84a7-aa7bb3e37f67'));
$userLoggedData = new SaidaFronteiraBuscarContaPorCodigo(
    id: '1dd966da-4228-474a-84a7-aa7bb3e37f67',
    nickname: 'Automático Negócio',
    email: 'email@automatico.negocio',
    businessID: '1dd966da-4228-474a-84a7-aa7bb3e37f67'
);
$container->set(EntidadeUsuarioLogado::class, EntidadeUsuarioLogado::buildUserLoggedEntity($userLoggedData));
$log = $container->get(Log::class);
$bankAccountRepository = $container->get(BankAccountRepository::class);

$maximumRetry = 10;

function logger(string $mensagem): void
{
    echo date('d/m/Y H:i:s', time()) . " [x] $mensagem \n";
}

$callback = function($message) use ($maximumRetry, &$eventBus, &$container, &$log, &$bankAccountRepository) {

    logger("Novo boleto para emissão: {$message->body}");

    $body = json_decode($message->body, true);

    $container->set('businessID', new IdentificacaoUnica($body['businessID']));
    $userLoggedData = new SaidaFronteiraBuscarContaPorCodigo(
        id: $body['businessID'],
        nickname: 'Automático Negócio',
        email: 'email@automatico.negocio',
        businessID: $body['businessID'],
    );
    $container->set(EntidadeUsuarioLogado::class, EntidadeUsuarioLogado::buildUserLoggedEntity($userLoggedData));

    // dont work parse message JSON to Array.
    if($body === null && json_last_error() !== JSON_ERROR_NONE){
        logger("Payload inválido, não foi possível o parse do JSON.");
        $log->log(Level::CRITICAL, "Payload inválido, não foi possível o parse do JSON.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    $tentativasRetry = 1;
    if(isset($body['try_attempt']) and is_numeric($body['try_attempt'])){
        $tentativasRetry += $body['try_attempt'] ;
    }

    if(!isset($body['pagador'])){
        logger("Payload inválido, está faltando o pagador, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando o pagador, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['beneficiarioFinal'])){
        logger("Payload inválido, está faltando o beneficiario Final, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando o beneficiario Final, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    $pagador = new InputBoundaryEmitirBoletoPagador(
        documento: $body['pagador']['documento'],
        tipoPessoa: $body['pagador']['tipoPessoa'],
        nome: $body['pagador']['nome'],
        endereco: $body['pagador']['endereco'],
        cidade: $body['pagador']['cidade'],
        uf: $body['pagador']['uf'],
        cep: $body['pagador']['cep'],
        telefone: $body['pagador']['telefone'],
        email: $body['pagador']['email'],
        enderecoNumero: $body['pagador']['enderecoNumero'],
        enderecoComplemento: $body['pagador']['enderecoComplemento'],
    );
    
    $beneficiarioFinal = new InputBoundaryEmitirBoletoBeneficiarioFinal(
        nome: $body['beneficiarioFinal']['nome'],
        documento: $body['beneficiarioFinal']['documento'],
        tipoPessoa: $body['beneficiarioFinal']['tipoPessoa'],
        cep: $body['beneficiarioFinal']['cep'],
        endereco: $body['beneficiarioFinal']['endereco'],
        bairro: $body['beneficiarioFinal']['bairro'],
        cidade: $body['beneficiarioFinal']['cidade'],
        uf: $body['beneficiarioFinal']['uf'],
    );

    if(!isset($body['seuNumero'])){
        logger("Payload inválido, está faltando o Seu Numero, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando o Seu Numero, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['valor'])){
        logger("Payload inválido, está faltando o Valor, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando o Valor, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['dataVencimento'])){
        logger("Payload inválido, está faltando a Data de Vencimento, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando a Data de Vencimento, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['mensagem'])){
        logger("Payload inválido, está faltando a Mensagem, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando a Mensagem, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['multa'])){
        logger("Payload inválido, está faltando a Multa, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando a Multa, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['juros'])){
        logger("Payload inválido, está faltando o Juros, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando o Juros, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['valorDescontoAntecipacao'])){
        logger("Payload inválido, está faltando o Valor Desconto Antecipação, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando o Valor Desconto Antecipação, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['composicaoBoletoTexto'])){
        logger("Payload inválido, está faltando a Composição do Boleto Texto, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando a Composição do Boleto Texto, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['tipoDesconto'])){
        logger("Payload inválido, está faltando o Tipo de Desconto, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando o Tipo de Desconto, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['tipoJuros'])){
        logger("Payload inválido, está faltando o Tipo de Juros, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando o Tipo de Juros, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    if(!isset($body['tipoMulta'])){
        logger("Payload inválido, está faltando o Tipo de Multa, será removido da fila.");
        $log->log(Level::CRITICAL, "Payload inválido, está faltando o Tipo de Multa, será removido da fila.");
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    $paramsEmissaoBoleto = new InputBoundaryEmitirBoleto(
        banco: $body['banco'],
        businessID: $body['businessID'],
        bankAccountID: $body['bankAccountID'],
        pagador: $pagador,
        beneficiarioFinal: $beneficiarioFinal,
        seuNumero: $body['seuNumero'],
        valor: $body['valor'],
        dataVencimento: $body['dataVencimento'],
        mensagem: $body['mensagem'],
        multa: $body['multa'],
        juros: $body['juros'],
        valorDescontoAntecipacao: $body['valorDescontoAntecipacao'],
        composicaoBoletoTexto: $body['composicaoBoletoTexto'],
        tipoDesconto: $body['tipoDesconto'],
        tipoJuros: $body['tipoJuros'],
        tipoMulta: $body['tipoMulta'],
    );

    try {

        $bankAPIImplementationNamespace = match($paramsEmissaoBoleto->banco){
            'Inter' => InterImplementation::class,
            default => throw new Exception('Banco não encontrado.')
        };


        $paramsBankAccount = $bankAccountRepository->getBankAccountByID($body['bankAccountID'], $body['businessID']);
        $bankAccountEntity = BankAccountEntity::buildBankAccountEntity($paramsBankAccount);

        $bankAPIImplementation = new $bankAPIImplementationNamespace(
            _log: $container->get(Log::class),
            env: $container->get(Environment::class),
            bankAccountEntity: $bankAccountEntity,
            clientHttp: $container->get(ClientHttp::class),
        );

        $respostaBanco = $bankAPIImplementation->emitirBoleto($paramsEmissaoBoleto); // OutputBoundaryEmitirBoleto

        if(empty($respostaBanco->codigoBarras)){
            logger("Boleto não foi emitido para o banco, está sem código de barras, será tentado novamente.");
            $log->log(Level::CRITICAL, "Boleto não foi emitido para o banco, está sem código de barras, será tentado novamente.");
            $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
            return;
        }

        $log->log(Level::INFO, "Boleto emitido com sucesso para o banco. " . json_encode($respostaBanco));
        logger("Boleto emitido com sucesso para o banco.");
        logger(json_encode($respostaBanco));

        $billingUseCase = $container->get(BillingUseCase::class);
        
        $params = new InputBoundaryEmitidoComSucesso(
            seuNumero: $respostaBanco->seuNumero,
            nossoNumero: $respostaBanco->nossoNumero,
            linhaDigitavel: $respostaBanco->linhaDigitavel,
            codigoBarras: $respostaBanco->codigoBarras
        );

        try {

            $billingUseCase->boletoEmitidoComSucesso($params);
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

            $log->log(Level::INFO, "Boleto foi aceito no banco e processado.");
            logger("Boleto foi aceito no banco e processado.");
        }catch(Exception $erro){
            
            $mensagemErro = $erro->getMessage();
            
            if(str_contains($mensagemErro, 'já foi aceito pelo banco')){
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

                $log->log(Level::INFO, "Parece que o boleto já foi aceito anteriormente.");

                echo "Parece que o boleto já foi aceito anteriormente.\n";
                return;
            }

            $log->log(Level::CRITICAL, "Erro ao processar o boleto emitido com sucesso: {$mensagemErro}");

            throw new Exception($mensagemErro);
        }

    }catch(Exception $e){

		dd($e->getMessage());

        // Ocorreu um erro ao enviar o email, vamos tentar novamente
        if($tentativasRetry >= $maximumRetry){

            try {

                $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);

                $log->log(Level::CRITICAL, "Erro ao emitir o boleto para o banco: {$e->getMessage()} e tentativa de Retry excedida");
                
                echo "Erro ao emitir o boleto para o banco: {$e->getMessage()} e tentativa de Retry excedida\n";
                return;

            }catch(Exception $e){
                echo "Exception --- Erro ao emitir o boleto ao banco: {$e->getMessage()}\n";
            }
        }

        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);

        sleep(5 * $tentativasRetry);

        $body['try_attempt'] = 0;
        $body['try_attempt'] += $tentativasRetry;

        $eventBus->publish(
            event: Event::EmitirBoleto,
            message: json_encode($body),
        );
    }
};

try {

    logger("Aguardando novos boletos");
    
    $eventBus->subscribe(
        event: Event::EmitirBoleto,
        callback: $callback
    );

}catch(Exception $e){
    echo "{$e->getMessage()}\n";
}
