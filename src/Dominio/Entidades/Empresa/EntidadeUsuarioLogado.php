<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Business;

use App\Dominio\Repositorios\Autenticacao\Fronteiras\OutputGetAccountByID;
use App\Dominio\ObjetoValor\Email;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Dominio\ObjetoValor\IdentificacaoUnica;

class EntidadeUsuarioLogado
{
    public function __construct(
        public readonly IdentificacaoUnica $id,
        public readonly IdentificacaoUnica $businessId,
        public NomeCompleto $name,
        public Email $email,
    ){}

    public static function buildUserLoggedEntity(OutputGetAccountByID $params): EntidadeUsuarioLogado
    {

        return new EntidadeUsuarioLogado(
            id: new IdentificacaoUnica($params->id),
            businessId: new IdentificacaoUnica($params->businessID),
            name: new NomeCompleto($params->nickname),
            email: new Email($params->email),
        );
    }
}