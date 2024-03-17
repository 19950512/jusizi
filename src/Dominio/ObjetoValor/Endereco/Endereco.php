<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor\Endereco;

use App\Dominio\ObjetoValor\TextoSimples;
use App\Dominio\ObjetoValor\Endereco\CEP;
use App\Dominio\ObjetoValor\Endereco\Estado;
use App\Dominio\ObjetoValor\Endereco\Pais;
use App\Dominio\ObjetoValor\Endereco\Localizacao\Latitude;
use App\Dominio\ObjetoValor\Endereco\Localizacao\Longitude;
use App\Dominio\ObjetoValor\Endereco\Localizacao\Localizacao;

final class Endereco
{
    public function __construct(
        public TextoSimples $rua,
        public TextoSimples $numero,
        public TextoSimples $bairro,
        public TextoSimples $cidade,
        public Estado $estado,
        public Pais $pais,
        public CEP $cep,
        public TextoSimples $complemento,
        public TextoSimples $referencia,
        public Localizacao $localizacao,
    ){}

    public function setParams(array $params): void
    {
        $numero = null;
        $rua = null;
        $cep = null;
        $complemento = null;
        $bairro = null;
        $cidade = null;
        $estado = null;
        $pais = new Pais('Brazil');
        $referencia = null;
        $localizacao = null;

        if(isset($params['numero']) and !empty($params['numero'])){
            $numero = new TextoSimples($params['numero']);
        }
        if(isset($params['rua']) and !empty($params['rua'])){
            $rua = new TextoSimples($params['rua']);
        }
        if(isset($params['cep']) and !empty($params['cep'])){
            $cep = new CEP($params['cep']);
        }
        if(isset($params['complemento']) and !empty($params['complemento'])){
            $complemento = new TextoSimples($params['complemento']);
        }
        if(isset($params['bairro']) and !empty($params['bairro'])){
            $bairro = new TextoSimples($params['bairro']);
        }
        if(isset($params['cidade']) and !empty($params['cidade'])){
            $cidade = new TextoSimples($params['cidade']);
        }
        if(isset($params['estado']) and !empty($params['estado'])){
            $estado = new Estado($params['estado']);
        }
        if(isset($params['pais']) and !empty($params['pais'])){
            $pais = new Pais($params['pais']);
        }
        if(isset($params['referencia']) and !empty($params['referencia'])){
            $referencia = new TextoSimples($params['referencia']);
        }
        if(isset($params['latitude'], $params['longitude']) and !empty($params['latitude']) and !empty($params['longitude'])){
            $localizacao = new Localizacao(
                latitude: new Latitude((float) $params['latitude']),
                longitude: new Longitude((float) $params['longitude'])
            );
        }

        $this->numero = $numero;
        $this->rua = $rua;
        $this->cep = $cep;
        $this->complemento = $complemento;
        $this->bairro = $bairro;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->pais = $pais;
        $this->referencia = $referencia;
        $this->localizacao = $localizacao;
    }

    public function get(): array
    {
        return [
            'logradouro' => $this->rua->get(),
            'numero' => $this->numero->get(),
            'bairro' => $this->bairro->get(),
            'cidade' => $this->cidade->get(),
            'estado' => $this->estado->get(),
            'pais' => $this->pais->get(),
            'cep' => $this->cep->get(),
            'complemento' => $this->complemento->get(),
            'referencia' => $this->referencia->get(),
            'localizacao' => $this->localizacao->get(),
        ];
    }
}