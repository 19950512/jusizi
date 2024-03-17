<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Errors;

use DI\Container;
use App\Infra\Api\Controllers\Middlewares\Controller;

class Erro404Controller extends Controller
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

        header("HTTP/1.0 404 Not Found");
    }
}

