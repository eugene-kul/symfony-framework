<?php

use App\Controllers\FuckController;
use App\Controllers\TestController;
use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', [
    'year' => null,
    '_controller' => [new FuckController(), 'index'],
]));
$routes->add('test', new Routing\Route('/test/{year}', [
    'year' => null,
    '_controller' => [new TestController(), 'index'],
]));

return $routes;
