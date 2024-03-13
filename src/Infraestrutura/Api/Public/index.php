<?php

namespace App\Infra\Api;

include_once __DIR__.'/../../../Config/autoload.php';

use App\Infra\Api\Router;
use App\Config\Containerapp;

$containerApp = Containerapp::getInstance();

$container = $containerApp->get(null);

$router = new Router(
    __SERVER: $_SERVER,
    container: $container
);