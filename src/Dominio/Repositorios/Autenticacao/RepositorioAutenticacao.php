<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios\Autenticacao;

use App\Dominio\Repositorios\Autenticacao\Fronteiras\EntradaFronteiraNovaConta;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\EntradaFronteiraNovaEmpresa;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraEmpresa;
use App\Dominio\Repositorios\Autenticacao\Fronteiras\SaidaFronteiraBuscarContaPorCodigo;

interface RepositorioAutenticacao
{
    public function buscarContaPorCodigo(string $contaCodigo): SaidaFronteiraBuscarContaPorCodigo;
    public function buscarContaPorEmail(string $email): SaidaFronteiraBuscarContaPorCodigo;
    public function contaExistePorEmail(string $email): bool;
    public function contaExistePorEmailESenha(string $email, string $senha): bool;

    public function buscarEmpresaPorCodigo(string $empresaCodigo): SaidaFronteiraEmpresa;
    public function empresaExistePorCodigo(string $empresaCodigo): bool;
    public function novaEmpresa(EntradaFronteiraNovaEmpresa $params): void;
    public function novaConta(EntradaFronteiraNovaConta $params): void;

    public function buscarToken(string $token, string $contaCodigo, string $empresaCodigo): string;
    public function novoToken(string $token, string $contaCodigo, string $empresaCodigo): void;
}