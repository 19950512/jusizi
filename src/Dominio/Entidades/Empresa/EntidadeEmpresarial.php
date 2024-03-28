<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Empresa;

use App\Dominio\Entidades\Empresa\Colaboradores\Colaboradores;
use App\Dominio\ObjetoValor\Apelido;
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

	public Colaboradores $colaboradores;
    public function __construct(
        readonly public IdentificacaoUnica $codigo,
        public Apelido $apelido,
        public DocumentoIdentificacao $numeroDocumento,
        public Endereco $endereco,
    ){}

    public static function instanciarEntidadeEmpresarial(SaidaFronteiraEmpresa $params): EntidadeEmpresarial
    {

		try {
			$apelido = new Apelido($params->nome);
		}catch (Exception $erro){
			throw new Exception("O Apelido da Entidade Empresarial '{$params->nome}' ID: $params->empresaCodigo não está válido. {$erro->getMessage()}");
		}

        $entidadeEmpresarial = new EntidadeEmpresarial(
            codigo: new IdentificacaoUnica($params->empresaCodigo),
	        apelido: $apelido,
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
		$entidadeEmpresarial->colaboradores = new Colaboradores();

		return $entidadeEmpresarial;
    }

    public function toArray(): array
    {
        return [
            'codigo' => $this->codigo->get(),
            'apelido' => $this->apelido->get(),
            'documentoTipo' => is_a($this->numeroDocumento, CNPJ::class) ? 'CNPJ' : 'CPF',
            'documentoNumero' => $this->numeroDocumento->get(),
        ];
    }
}