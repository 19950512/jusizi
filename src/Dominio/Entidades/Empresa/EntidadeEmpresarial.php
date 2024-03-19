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
use Exception;

class EntidadeEmpresarial
{
    public function __construct(
        readonly public IdentificacaoUnica $codigo,
        public NomeCompleto $nomeCompleto,
        public DocumentoIdentificacao $numeroDocumento,
        public Endereco $endereco,
    ){}

    public static function instanciarEntidadeEmpresarial(SaidaFronteiraEmpresa $params): EntidadeEmpresarial
    {

		try {
			$nomeCompleto = new NomeCompleto($params->nome);
		}catch (Exception $erro){
			throw new Exception("O nome completo da Entidade Empresarial '{$params->nome}' ID: $params->empresaCodigo não está válido. {$erro->getMessage()}");
		}

        return new EntidadeEmpresarial(
            codigo: new IdentificacaoUnica($params->empresaCodigo),
	        nomeCompleto: $nomeCompleto,
            numeroDocumento: new CNPJ($params->numeroDocumento),
            endereco:  new Endereco(
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
            'codigo' => $this->codigo->get(),
            'nomeFantasia' => $this->nomeCompleto->get(),
            'documentoTipo' => is_a($this->numeroDocumento, CNPJ::class) ? 'CNPJ' : 'CPF',
            'documentoNumero' => $this->numeroDocumento->get(),
        ];
    }
}