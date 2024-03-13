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
        public TextoSimples $street,
        public TextoSimples $number,
        public TextoSimples $neighborhood,
        public TextoSimples $city,
        public Estado $state,
        public Pais $country,
        public CEP $cep,
        public TextoSimples $complement,
        public TextoSimples $reference,
        public Localizacao $localization,
    ){}

    public function setParams(array $params): void
    {

        $number = null;
        $street = null;
        $cep = null;
        $complement = null;
        $neighborhood = null;
        $city = null;
        $state = null;
        $country = new Pais('Brazil');
        $reference = null;
        $localization = null;

        if(isset($params['number']) and !empty($params['number'])){
            $number = new TextoSimples($params['number']);
        }
        if(isset($params['street']) and !empty($params['street'])){
            $street = new TextoSimples($params['street']);
        }
        if(isset($params['cep']) and !empty($params['cep'])){
            $cep = new CEP($params['cep']);
        }
        if(isset($params['complement']) and !empty($params['complement'])){
            $complement = new TextoSimples($params['complement']);
        }
        if(isset($params['neighborhood']) and !empty($params['neighborhood'])){
            $neighborhood = new TextoSimples($params['neighborhood']);
        }
        if(isset($params['city']) and !empty($params['city'])){
            $city = new TextoSimples($params['city']);
        }
        if(isset($params['state']) and !empty($params['state'])){
            $state = new Estado($params['state']);
        }
        if(isset($params['country']) and !empty($params['country'])){
            $country = new Pais($params['country']);
        }
        if(isset($params['reference']) and !empty($params['reference'])){
            $reference = new TextoSimples($params['reference']);
        }
        if(isset($params['latitude'], $params['longitude']) and !empty($params['latitude']) and !empty($params['longitude'])){
            $localization = new Localizacao(
                latitude: new Latitude((float) $params['latitude']),
                longitude: new Longitude((float) $params['longitude'])
            );
        }

        $this->number = $number;
        $this->street = $street;
        $this->cep = $cep;
        $this->complement = $complement;
        $this->neighborhood = $neighborhood;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->reference = $reference;
        $this->localization = $localization;
    }

    public function get(): array
    {
        return [
            'street' => $this->street->get(),
            'number' => $this->number->get(),
            'neighborhood' => $this->neighborhood->get(),
            'city' => $this->city->get(),
            'state' => $this->state->get(),
            'country' => $this->country->get(),
            'cep' => $this->cep->get(),
            'complement' => $this->complement->get(),
            'reference' => $this->reference->get(),
            'localization' => $this->localization->get(),
        ];
    }
}