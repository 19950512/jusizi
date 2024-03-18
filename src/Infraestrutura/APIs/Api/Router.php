<?php

declare(strict_types=1);

namespace App\Infraestrutura\APIs\Api;

use DI\Container;

class Router
{

    public $controller;
    public $action;

    public function __construct(
        private array $__SERVER,
        private Container $container
    ){

        $uri = explode('/', $this->__SERVER['REQUEST_URI']);
        $controllerName = explode('?', ($uri[1] ?? 'Index'))[0];
        $controllerName = ucfirst(empty($controllerName) ? 'Index' : $controllerName);

        $action = ($uri[2] ?? 'Index');
        $this->action = ucfirst(empty($action) ? 'Index' : $action);

        $controllerNameSpace = "App\Infra\Api\Controllers\Errors\Erro404Controller";
       
        $pathController = __DIR__."/Controladores/$controllerName/{$controllerName}Controller.php";

        if(is_file($pathController)){
            $controllerNameSpace = "App\Infra\Api\Controllers\\$controllerName\\{$controllerName}Controller";

            $this->controller = new $controllerNameSpace(
                container: $this->container
            );
        }else{
            $this->controller = new $controllerNameSpace(
                container: $this->container
            );
        }

        if(!method_exists($this->controller, $this->action)){

            $controllerNameSpace = "App\Infra\Api\Controllers\Errors\Erro404Controller";
            $this->controller = new $controllerNameSpace(
                container: $this->container
            );
            $this->action = 'Index';
        }

        $this->controller->{$this->action}();
    }
}