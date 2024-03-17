<?php

declare(strict_types=1);

namespace App\Infra\Api\Controllers\Middlewares;

use DI\Container;

abstract class Controller extends Authorization
{

    public $method;

    public function __construct(
        private Container $container
    ){

        $this->method = $_SERVER['REQUEST_METHOD'] ?? '';

        if(is_array($_POST) and count($_POST) == 0){
            $json = file_get_contents('php://input');
            $_POST = json_decode(json_decode(json_encode($json), true), true);
        }

        parent::__construct(
            container: $this->container
        );
    }

    public function response(array $data)
    {

        header('Content-Type: application/json; charset=utf-8');
        header('X-Powered-By: Hanabi');

        if(isset($data['statusCode']) and is_numeric($data['statusCode'])){
            header("HTTP/1.0 {$data['statusCode']}");
            unset($data['statusCode']);
        }

        echo json_encode($data['data'] ?? $data);
    }
}
