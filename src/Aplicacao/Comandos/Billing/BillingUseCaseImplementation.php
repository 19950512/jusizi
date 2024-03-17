<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing;

use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitidoComSucesso;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoleto;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoletoBeneficiarioFinal;
use App\Application\Commands\Billing\Fronteiras\InputBoundaryEmitirBoletoPagador;
use App\Application\Commands\Billing\Fronteiras\InputPushtBilling;
use App\Application\Commands\Log\Enumerados\Level;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Aplicacao\Compartilhado\HTTP\ClientHttp;
use App\Dominio\Entidades\Business\EntidadeEmpresarial;
use App\Dominio\Entidades\ChartOfAccount\ChartOfAccountEntity;
use App\Dominio\Entidades\Contract\ContractEntity;
use App\Dominio\Entidades\Financial\BillingComposition;
use App\Dominio\Entidades\Financial\BillingEntity;
use App\Dominio\Entidades\OrderService\OrderServiceEntity;
use App\Dominio\Repositorios\BankAccount\BankAccountRepository;
use App\Dominio\Repositorios\Billing\BillingRepository;
use App\Dominio\Repositorios\Billing\Fronteiras\InputAcceptByBank;
use App\Dominio\Repositorios\Billing\Fronteiras\InputEmitBilling;
use App\Dominio\Repositorios\Billing\Fronteiras\InputPushBilling;
use App\Dominio\Repositorios\ChartOfAccount\ChartOfAccountRepository;
use App\Dominio\Repositorios\Client\ClientRepository;
use App\Dominio\Repositorios\Contract\ContractRepository;
use App\Dominio\Repositorios\Token\TokenRepository;
use App\Dominio\ObjetoValor\CPF;
use App\Dominio\ObjetoValor\Description;
use App\Dominio\ObjetoValor\Value;
use App\Infra\EventBus\Event;
use App\Shared\Environment;
use App\Shared\EventBus\EventBus;
use DateInterval;
use DateTime;
use Exception;

class BillingUseCaseImplementation implements BillingUseCase
{

    public function __construct(
        readonly private IdentificacaoUnica                     $businessID,
        readonly private ClientRepository         $_clientRepository,
        readonly private ContractRepository       $_contractRepository,
        readonly private BillingRepository        $_billingRepository,
        readonly private EntidadeEmpresarial      $_businessEntity,
        readonly private BankAccountRepository    $_bankAccountRepository,
        readonly private ChartOfAccountRepository $_chartOfAccountRepository,
        readonly private TokenRepository          $_tokenRepository,
        readonly private ClientHttp               $_clientHttp,
        readonly private Environment              $_env,
        readonly private EventBus                 $eventBus,
        readonly private Log                      $_log,
    ){}

    public function pushBilling(InputPushtBilling $params): void
    {
        try {

            $contractData = $this->_contractRepository->getContractByID($params->contractID);
            $contractEntity = ContractEntity::buildContractEntity($contractData);

        }catch(Exception $e) {
            throw new Exception('O contrato informado não foi encontrado');
        }

        try {

            $chartOfAccountData = $this->_chartOfAccountRepository->getByID($params->chartOfAccountID);
            $chartOfAccountEntity = ChartOfAccountEntity::buildChartOfAccountEntity($chartOfAccountData);

        }catch(Exception $e) {
            throw new Exception('Plano de conta informado não foi encontrado');
        }

        try {

            $description = new Description($params->description);
        
        }catch(Exception $e) {
            throw new Exception('A descrição da cobrança está inválida');
        }

        try {

            $value = new Value((float) $params->value);
        
        }catch(Exception $e) {
            throw new Exception('O valor da cobrança está inválido');
        }

        try {

            $billingCompositionEntity = new BillingComposition(
                chartOfAccount: $chartOfAccountEntity,
                description: $description,
                value: $value,
                contract: $contractEntity,
            );
        
        }catch(Exception $e) {
            throw new Exception('Não foi possível compor a fatura');
        }

        try {

            $codeBillingPush = new IdentificacaoUnica();

            $billingPushParams = new InputPushBilling(
                businessID: $this->businessID->get(),
                code: $codeBillingPush->get(),
                contractID: $contractEntity->id->get(),
                charOfAccountID: $billingCompositionEntity->chartOfAccount->id->get(),
                value: (string) $billingCompositionEntity->value->get(),
                description: $billingCompositionEntity->description->get(),
            );

            $this->_billingRepository->pushBilling($billingPushParams);

        }catch(Exception $e) {
            
            $this->_log->log(
                level: Level::CRITICAL,
                message: $e->getMessage()
            );

            throw new Exception('Não foi possível realizar o lançamento da fatura');
        }
    }

    public function emitBillingToday(): void
    {

        $contractsData = $this->_contractRepository->getContractsEmitBillingToday();

        foreach($contractsData as $key => $contract){
            
            $contractData = $this->_contractRepository->getContractByID($contract['codigo']);
            
            $contractEntity = ContractEntity::buildContractEntity($contractData);

            if($contractEntity->availability->get() === false){
                // Esse contrato não está ativo
                continue;
            }

            if($contractEntity->diaEmissaoCobranca !== (int) date('d')){
                // Esse contrato não deve ser cobrado hoje
                continue;
            }

            if($this->_billingRepository->contractSentBillingToDate($contractEntity->id->get(), date('Y-m-d'))){
                // Esse contrato já foi cobrado nesse dia
                continue;
            }

            $this->registerBilling($contractEntity);
        }
    }

    public function boletoEmitidoComSucesso(InputBoundaryEmitidoComSucesso $params): void
    {

        try {
            $boletoData = $this->_billingRepository->getBoletoBySeuNumero($params->seuNumero);
        }catch(Exception $erro){

            $this->_log->log(
                level: Level::CRITICAL,
                message: $erro->getMessage()
            );
            throw new Exception($erro->getMessage());
        }

        $boletoEntity = BillingEntity::buildBillingEntity($boletoData);

        if($boletoEntity->bankAccepted){
            $this->_log->log(
                level: Level::INFO,
                message: "O boleto {$boletoEntity->seuNumero} já foi aceito pelo banco anteriormente."
            );
            throw new Exception("O boleto {$boletoEntity->seuNumero} já foi aceito pelo banco anteriormente.");
        }

        $boletoEntity->bankAccepted(
            barCode: $params->codigoBarras,
            nossoNumero: $params->nossoNumero,
            linhaDigitavel: $params->linhaDigitavel
        );

        $paramsBillingRepository = new InputAcceptByBank(
            seuNumero: $boletoEntity->seuNumero,
            nossoNumero: $boletoEntity->nossoNumero,
            linhaDigitavel: $boletoEntity->linhaDigitavel,
            barCode: $boletoEntity->barCode
        );
        $this->_billingRepository->billingAcceptByBank($paramsBillingRepository);

        $paramsEnviarEmail = [
            'titulo' => "Querido(a) {$boletoEntity->contract->client->name->get()}, seu boleto está disponível para pagamento",
            'nome' => $boletoEntity->contract->client->name->get(),
            'email' => $boletoEntity->contract->client->email->get(),
            'body' => "<h1>Dear {$boletoEntity->contract->client->name->get()}</h1><p>Seu boleto está disponível para pagamento.</p>",
        ];

        $this->_log->log(
            level: Level::INFO,
            message: "O boleto {$boletoEntity->seuNumero} foi aceito pelo banco. e estamos enviando para a fila de email, um e-mail para o cliente {$boletoEntity->contract->client->name->get()}."
        );
        $this->eventBus->publish(
            event: Event::EnviarEmail,
            message: json_encode($paramsEnviarEmail)
        );

        if($boletoEntity->contract->emitirNFSe){

            $this->_log->log(
                level: Level::INFO,
                message: "O boleto {$boletoEntity->seuNumero} foi aceito pelo banco. e estamos enviando para a fila NFSe, criar uma NFSe para o cliente {$boletoEntity->contract->client->name->get()}."
            );

            $paramsEmitirNFSe = [];

            $this->eventBus->publish(
                event: Event::EmitirNfse,
                message: json_encode($paramsEmitirNFSe),
            );
        }
    }

    public function registerBillingOrderService(OrderServiceEntity $orderServiceEntity): void
    {

        $billingsByOrderService = $this->_billingRepository->getBillingsByOrderServiceID($orderServiceEntity->code->get());

        if(count($billingsByOrderService) <= 0){
            // Esse contrato não possui cobranças

            $this->_log->log(
                level: Level::INFO,
                message: "A Ordem de serviço {$orderServiceEntity->code->get()} não possui cobranças."
            );
            throw new Exception('A ordem de serviço não possui cobranças');
        }

        $totalBilling = 0;
        $compositionBilling = [];

        foreach($billingsByOrderService as $key => $billing){
            $chartOfAccountData = $this->_chartOfAccountRepository->getByID($billing['plano_de_contas_id']);
            $chartOfAccountEntity = ChartOfAccountEntity::buildChartOfAccountEntity($chartOfAccountData);

            $description = new Description($billing['descricao']);

            $valorDo = (float) $billing['valor'];

            $value = new Value($valorDo);

            $compositionBilling[] = new BillingComposition(
                chartOfAccount: $chartOfAccountEntity,
                description: $description,
                value: $value,
                orderService: $orderServiceEntity,
            );
            $totalBilling += $valorDo;
        }

        $compositionBillingArray = array_map(function($item){
            return $item->description->get();
        }, $compositionBilling);


        $descriptionBillingComposition = new Description(implode(PHP_EOL, $compositionBillingArray));
        $valueBilling = new Value($totalBilling);

        try {

            $pagador = new InputBoundaryEmitirBoletoPagador(
                documento: $orderServiceEntity->client->typeRegister->get(),
                tipoPessoa: CPF::validation($orderServiceEntity->client->typeRegister->get()) ? 'FISICA' : 'JURIDICA',
                nome: $orderServiceEntity->client->name->get(),
                endereco: '',
                cidade: '',
                uf: '',
                cep: '',
                telefone: $orderServiceEntity->client->phone->get(),
                email: $orderServiceEntity->client->email->get(),
            );

            $beneficiarioFinal = new InputBoundaryEmitirBoletoBeneficiarioFinal
            (
                nome: $this->_businessEntity->tradeName->get(),
                documento: $this->_businessEntity->document->get(),
                tipoPessoa: CPF::validation($this->_businessEntity->document->get()) ? 'FISICA' : 'JURIDICA',
                cep: $this->_businessEntity->address->cep->get(),
                endereco: $this->_businessEntity->address->street->get(),
                bairro: $this->_businessEntity->address->neighborhood->get(),
                cidade: $this->_businessEntity->address->city->get(),
                uf: $this->_businessEntity->address->state->getUF(),
            );

            $seuNumero = new IdentificacaoUnica();

            $dataVencimento = new DateTime();
            $dataVencimento->add(new DateInterval('P10D'));

            $paramsEmitirBoleto = new InputBoundaryEmitirBoleto(
                banco: $orderServiceEntity->bankAccountEntity->bank->name->get(),
                businessID: $this->businessID->get(),
                bankAccountID: $orderServiceEntity->bankAccountEntity->id->get(),
                pagador: $pagador,
                beneficiarioFinal: $beneficiarioFinal,
                seuNumero: $seuNumero->get(),
                valor: (string) $valueBilling->get(),
                dataVencimento: $dataVencimento->format('Y-m-d'),
                mensagem: $descriptionBillingComposition->get(),
                multa: '0',
                juros: '0',
                valorDescontoAntecipacao: '0',
                composicaoBoletoTexto: $descriptionBillingComposition->get(),
                tipoDesconto: 'ISENTO',
                tipoJuros: 'ISENTO',
                tipoMulta: 'ISENTO',
            );

            $paramsRepository = new InputEmitBilling(
                businessID: $this->businessID->get(),
                pagadorID: $orderServiceEntity->client->id->get(),
                seuNumero: $paramsEmitirBoleto->seuNumero,
                valor: $paramsEmitirBoleto->valor,
                dataVencimento: $paramsEmitirBoleto->dataVencimento,
                mensagem: $paramsEmitirBoleto->mensagem,
                multa: $paramsEmitirBoleto->multa,
                juros: $paramsEmitirBoleto->juros,
                valorDescontoAntecipacao: $paramsEmitirBoleto->valorDescontoAntecipacao,
                composicaoBoletoTexto: $paramsEmitirBoleto->composicaoBoletoTexto,
                tipoDesconto: $paramsEmitirBoleto->tipoDesconto,
                tipoJuros: $paramsEmitirBoleto->tipoJuros,
                tipoMulta: $paramsEmitirBoleto->tipoMulta,
                orderServiceID: $orderServiceEntity->code->get(),
            );

            $this->_billingRepository->emitBilling($paramsRepository);

            $this->_log->log(
                level: Level::INFO,
                message: "O boleto {$seuNumero->get()} foi gerado para o cliente {$orderServiceEntity->client->name->get()} e estamos enviando para a fila de cobrança."
            );

            $this->eventBus->publish(
                event: Event::EmitirBoleto,
                message: json_encode($paramsEmitirBoleto),
            );

        }catch(Exception $e){

            $this->_log->log(
                level: Level::CRITICAL,
                message: $e->getMessage()
            );

            throw new Exception('Não foi possível emitir a cobrança - '.$e->getMessage());
        }
    }

    public function registerBilling(ContractEntity $contractEntity): void
    {

        $billingsByContract = $this->_billingRepository->getBillingsByContractID($contractEntity->id->get());

        if(count($billingsByContract) <= 0){
            // Esse contrato não possui cobranças

            $this->_log->log(
                level: Level::INFO,
                message: "O contrato {$contractEntity->id->get()} não possui cobranças."
            );
            throw new Exception('Esse contrato não possui cobranças');
        }

        $totalBilling = 0;
        $compositionBilling = [];
        foreach($billingsByContract as $key => $billing){

            $chartOfAccountData = $this->_chartOfAccountRepository->getByID($billing['plano_de_contas_id']);
            $chartOfAccountEntity = ChartOfAccountEntity::buildChartOfAccountEntity($chartOfAccountData);

            $description = new Description($billing['descricao']);

            $value = new Value((float) $billing['valor']);

            $compositionBilling[] = new BillingComposition(
                chartOfAccount: $chartOfAccountEntity,
                description: $description,
                value: $value,
                contract: $contractEntity,
            );
            $totalBilling += (float) $billing['valor'];
        }

        $compositionBillingArray = array_map(function($item){
            return $item->description->get();
        }, $compositionBilling);

        $descriptionBillingComposition = new Description(implode(PHP_EOL, $compositionBillingArray));
        $valueBilling = new Value($totalBilling);

        try {

            $pagador = new InputBoundaryEmitirBoletoPagador(
                documento: $contractEntity->client->typeRegister->get(),
                tipoPessoa: CPF::validation($contractEntity->client->typeRegister->get()) ? 'FISICA' : 'JURIDICA',
                nome: $contractEntity->client->name->get(),
                endereco: '',
                cidade: '',
                uf: '',
                cep: '',
                telefone: $contractEntity->client->phone->get(),
                email: $contractEntity->client->email->get(),
            );

            $beneficiarioFinal = new InputBoundaryEmitirBoletoBeneficiarioFinal
            (
                nome: $this->_businessEntity->tradeName->get(),
                documento: $this->_businessEntity->document->get(),
                tipoPessoa: CPF::validation($this->_businessEntity->document->get()) ? 'FISICA' : 'JURIDICA',
                cep: $this->_businessEntity->address->cep->get(),
                endereco: $this->_businessEntity->address->street->get(),
                bairro: $this->_businessEntity->address->neighborhood->get(),
                cidade: $this->_businessEntity->address->city->get(),
                uf: $this->_businessEntity->address->state->getUF(),
            );

            $seuNumero = new IdentificacaoUnica();

            $dataVencimento = new DateTime();
            $dataVencimento->add(new DateInterval('P'.$contractEntity->diaEmissaoCobranca.'D'));

            $paramsEmitirBoleto = new InputBoundaryEmitirBoleto(
                banco: $contractEntity->bankAccount->bank->name->get(),
                businessID: $this->businessID->get(),
                bankAccountID: $contractEntity->bankAccount->id->get(),
                pagador: $pagador,
                beneficiarioFinal: $beneficiarioFinal,
                seuNumero: $seuNumero->get(),
                valor: (string) $valueBilling->get(),
                dataVencimento: $dataVencimento->format('Y-m-d'),
                mensagem: $descriptionBillingComposition->get(),
                multa: '0',
                juros: '0',
                valorDescontoAntecipacao: '0',
                composicaoBoletoTexto: $descriptionBillingComposition->get(),
                tipoDesconto: 'ISENTO',
                tipoJuros: 'ISENTO',
                tipoMulta: 'ISENTO',
            );

            $paramsRepository = new InputEmitBilling(
                businessID: $this->businessID->get(),
                pagadorID: $contractEntity->client->id->get(),
                seuNumero: $paramsEmitirBoleto->seuNumero,
                valor: $paramsEmitirBoleto->valor,
                dataVencimento: $paramsEmitirBoleto->dataVencimento,
                mensagem: $paramsEmitirBoleto->mensagem,
                multa: $paramsEmitirBoleto->multa,
                juros: $paramsEmitirBoleto->juros,
                valorDescontoAntecipacao: $paramsEmitirBoleto->valorDescontoAntecipacao,
                composicaoBoletoTexto: $paramsEmitirBoleto->composicaoBoletoTexto,
                tipoDesconto: $paramsEmitirBoleto->tipoDesconto,
                tipoJuros: $paramsEmitirBoleto->tipoJuros,
                tipoMulta: $paramsEmitirBoleto->tipoMulta,
                contractID: $contractEntity->id->get(),
            );

            $this->_billingRepository->emitBilling($paramsRepository);

            $this->_log->log(
                level: Level::INFO,
                message: "O boleto {$seuNumero->get()} foi gerado para o cliente {$contractEntity->client->name->get()} e estamos enviando para a fila de cobrança."
            );

            $this->eventBus->publish(
                event: Event::EmitirBoleto,
                message: json_encode($paramsEmitirBoleto),
            );

        }catch(Exception $e){
            $this->_log->log(
                level: Level::CRITICAL,
                message: $e->getMessage()
            );

            throw new Exception('Não foi possível emitir a cobrança');
        }
    }
}