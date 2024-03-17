<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Index;

use DI\Container;
use App\Infra\Api\Controllers\Middlewares\Controller;

class IndexController extends Controller
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
        
    }
}

