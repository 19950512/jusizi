<?php

declare(strict_types=1);

namespace App\Dominio\Entidades\Empresa;

use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraBuscarContaPorCodigo;
use App\Dominio\ObjetoValor\Email;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Dominio\ObjetoValor\IdentificacaoUnica;
use Exception;

class EntidadeUsuarioLogado
{
    public function __construct(
        public readonly IdentificacaoUnica $codigo,
        public readonly IdentificacaoUnica $empresaCodigo,
        public NomeCompleto $nomeCompleto,
        public Email $email,
    ){}

    public static function instanciarEntidadeUsuarioLogado(SaidaFronteiraBuscarContaPorCodigo $params): EntidadeUsuarioLogado
    {

        try {
            $nomeCompleto = new NomeCompleto($params->nomeCompleto);
        }catch (Exception $erro) {
            throw new Exception("Colaborador não possui nome completo válido. ({$params->nomeCompleto} - Codigo: {$params->contaCodigo}) - {$erro->getMessage()}");
        }

        try {
            $email = new Email($params->email);
        }catch (Exception $erro) {
            throw new Exception("Colaborador não possui email válido. ({$params->email} - Codigo: {$params->contaCodigo}) - {$erro->getMessage()}");
        }

        return new EntidadeUsuarioLogado(
            codigo: new IdentificacaoUnica($params->contaCodigo),
            empresaCodigo: new IdentificacaoUnica($params->empresaCodigo),
            nomeCompleto: $nomeCompleto,
            email: $email,
        );
    }
}