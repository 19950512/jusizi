<?php

declare(strict_types=1);

namespace App\Application\Commands\Business;

use App\Dominio\ObjetoValor\Email;
use App\Dominio\ObjetoValor\NomeCompleto;
use App\Application\Commands\Share\IdentificacaoUnica;
use App\Dominio\Repositorios\Business\BusinessRepository;
use App\Application\Queries\Business\BusinessQueriesUsecase;
use App\Application\Queries\Business\Fronteiras\InputBoundaryQuerieCreateAccount;
use App\Application\Commands\Business\Fronteiras\InputBoundaryCreateNewColaborador;
use App\Dominio\Entidades\Business\EntidadeEmpresarial;
use App\Dominio\Repositorios\Business\Fronteiras\InputBoundaryCreateNewColaboradorRepo;

class BusinessUsecaseImplementation implements BusinessUsecase
{

    public function __construct(
        private BusinessRepository           $businessRepository,
        private BusinessQueriesUsecase       $_businessQueriesUsecase,
        private IdentificacaoUnica                         $businessID,
        private readonly EntidadeEmpresarial $businessEntity,
    ){}


    public function createNewColaborador(InputBoundaryCreateNewColaborador $params): void
    {

        $name = new NomeCompleto($params->nome);

        $email = new Email($params->email);

        $code = new IdentificacaoUnica();

        $params = new InputBoundaryCreateNewColaboradorRepo(
            code: $code->get(),
            nome: $name->get(),
            email: $email->get(),
            businessID: $this->businessID->get()
        );

        $this->businessRepository->createNewColaborador($params);

        $paramsQueriesNewAccount = new InputBoundaryQuerieCreateAccount(
            business: $this->businessEntity,
            code: $code->get(),
            name: $name->get(),
            email: $email->get(),
            phone: ''
        );
        $this->_businessQueriesUsecase->createAccount($paramsQueriesNewAccount);
    }
}