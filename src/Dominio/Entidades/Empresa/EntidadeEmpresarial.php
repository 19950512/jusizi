<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Empresa;

use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraEmpresa;
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

    public static function instanciarEntidadeEmpresarial(SaidaFronteiraEmpresa $params): EntidadeEmpresarial
    {

        return new EntidadeEmpresarial(
            code: new IdentificacaoUnica($params->empresaCodigo),
            tradeName: new NomeCompleto('Teste Aqui'),
            document: new CNPJ('70174202000135'),
            address:  new Endereco(
                rua: new TextoSimples(''),
                numero: new TextoSimples(''),
                bairro: new TextoSimples(''),
                cidade: new TextoSimples('Marau'),
                estado: new Estado('RS'),
                pais: new Pais('BR'),
                cep: new CEP('99150-000'),
                complemento: new TextoSimples(''),
                referencia: new TextoSimples(''),
                localizacao: new Localizacao(
                    latitude: new Latitude(0),
                    longitude: new Longitude(0),
                ),
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'codigo' => $this->code->get(),
            'nomeFantasia' => $this->tradeName->get(),
            'documentoTipo' => is_a($this->document, CNPJ::class) ? 'CNPJ' : 'CPF',
            'documentoNumero' => $this->document->get(),
        ];
    }
}