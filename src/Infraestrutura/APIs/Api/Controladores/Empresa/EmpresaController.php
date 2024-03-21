<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Api\Controladores\Empresa;

use App\Infraestrutura\APIs\Api\Controladores\Middlewares\Controller;
use DI\Container;
use Exception;

class EmpresaController extends Controller
{


    public function __construct(
        private Container $container
    ){

        parent::__construct(
            container: $this->container
        );
    }

	public function index()
	{
		$this->response([
			'mensagem' => 'Bem vindo a API'
		]);
	}

}

