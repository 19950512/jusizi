<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Business;

use App\Dominio\Repositorios\Autenticacao\Fronteiras\OutputBoundaryBusiness;
use App\Dominio\ObjetoValor\Endereco\Endereco;
use App\Dominio\ObjetoValor\Endereco\CEP;
use App\Dominio\ObjetoValor\Endereco\Pais;
use App\Dominio\ObjetoValor\Endereco\Localizacao\Latitude;
use App\Dominio\ObjetoValor\Endereco\Localizacao\Localizacao;
use App\Dominio\ObjetoValor\Endereco\Localizacao\Longitude;
use App\Dominio\ObjetoValor\Endereco\Estado;
use App\Dominio\ObjetoValor\CNPJ;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Dominio\ObjetoValor\TextoSimples;
use App\Dominio\ObjetoValor\DocumentoIdentificacao;
use App\Dominio\ObjetoValor\IdentificacaoUnica;

class EntidadeEmpresarial
{
    public function __construct(
        public IdentificacaoUnica $code,
        public NomeCompleto $tradeName,
        public DocumentoIdentificacao $document,
        public Endereco $address,
    ){}

    public static function buildBusinessEntity(OutputBoundaryBusiness $params): EntidadeEmpresarial
    {

        return new EntidadeEmpresarial(
            code: new IdentificacaoUnica($params->businessID),
            tradeName: new NomeCompleto('Teste Aqui'),
            document: new CNPJ('70174202000135'),
            address:  new Endereco(
                street: new TextoSimples(''),
                number: new TextoSimples(''),
                neighborhood: new TextoSimples(''),
                city: new TextoSimples('Marau'),
                state: new Estado('RS'),
                country: new Pais('BR'),
                cep: new CEP('99150-000'),
                complement: new TextoSimples(''),
                reference: new TextoSimples(''),
                localization: new Localizacao(
                    latitude: new Latitude(0),
                    longitude: new Longitude(0),
                ),
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code->get(),
            'tradeName' => $this->tradeName->get(),
            'typeDocument' => is_a($this->document, CNPJ::class) ? 'CNPJ' : 'CPF',
            'document' => $this->document->get(),
        ];
    }
}