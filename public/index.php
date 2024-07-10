<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$routes = include __DIR__.'/../routes/web.php';
$container = include __DIR__.'/../src/container.php';

$request = Request::createFromGlobals();
//$response = $container->get('app')->handle($request);
$response = $container->get('appCache')->handle($request);

$response->send();