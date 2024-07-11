<?php

use App\Controllers\FuckController;
use App\Controllers\MainController;
use App\Controllers\TestController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('main', new Route('/', ['_controller' => [new MainController(), 'index']]));

$routes->add('leap_year', new Route('/is_leap_year/{year}', [
    'year' => null,
    '_controller' => [new FuckController(), 'index'],
]));
$routes->add('test', new Route('/test/{year}', [
    'year' => null,
    '_controller' => [new TestController(), 'index'],
]));

return $routes;