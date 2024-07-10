<?php

use App\EventListeners\GoogleListener;
use Barker\Events\Listeners\StringResponseListener;
use Barker\Main\App;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ErrorListener as SymfonyErrorListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

/** @var Routing\RouteCollection $routes */

$container = new ContainerBuilder();
$container->register('context', RequestContext::class);
$container->register('matcher', UrlMatcher::class)->setArguments([$routes, new Reference('context')]);
$container->register('request_stack', RequestStack::class);
$container->register('controller_resolver', ControllerResolver::class);
$container->register('argument_resolver', ArgumentResolver::class);

$container->register('listener.router', RouterListener::class)->setArguments([new Reference('matcher'), new Reference('request_stack')]);
$container->register('listener.response', ResponseListener::class)->setArguments(['UTF-8']);
$container->register('listener.exception', SymfonyErrorListener::class)->setArguments(['Barker\Events\Listeners\EventListener::exception']);
$container->register('listener.string_response', StringResponseListener::class);
$container->register('listener.google', GoogleListener::class);

$container->register('dispatcher', EventDispatcher::class)
    ->addMethodCall('addSubscriber', [new Reference('listener.router')])
    ->addMethodCall('addSubscriber', [new Reference('listener.response')])
    ->addMethodCall('addSubscriber', [new Reference('listener.exception')])

    ->addMethodCall('addSubscriber', [new Reference('listener.string_response')])
    ->addMethodCall('addSubscriber', [new Reference('listener.google')])
;

$container->register('app', App::class)
    ->setArguments([
        new Reference('dispatcher'),
        new Reference('controller_resolver'),
        new Reference('request_stack'),
        new Reference('argument_resolver'),
    ]);

$container->register('store', Store::class)->setArguments([__DIR__.'/../cache']);
$container->register('esi', Esi::class);

$container->register('appCache', HttpCache::class)
    ->setArguments([
        new Reference('app'),
        new Reference('store'),
        new Reference('esi'),
    ]);

return $container;