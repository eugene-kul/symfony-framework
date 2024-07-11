<?php

namespace Barker\Main;

use App\EventListeners\GoogleListener;
use Barker\Events\Listeners\StringResponseListener;
use Barker\Support\Env;
use Composer\Autoload\ClassLoader;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ErrorListener as SymfonyErrorListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class App
{
    protected Response $response;
    protected string $basePath;
    protected ContainerBuilder $container;
    private Definition $dispatcher;
    private Request $request;

    public function __construct()
    {
        $this->basePath = dirname(array_keys(ClassLoader::getRegisteredLoaders())[0]);

        $this->container = new ContainerBuilder();

        $this->dispatcher = $this->container->register('dispatcher', EventDispatcher::class);

        $this->container->register('context', RequestContext::class);
        $this->container->register('matcher', UrlMatcher::class)->setArguments([include "$this->basePath/routes/web.php", new Reference('context')]);
        $this->container->register('request_stack', RequestStack::class);
        $this->container->register('controller_resolver', ControllerResolver::class);
        $this->container->register('argument_resolver', ArgumentResolver::class);

        $this->container->setParameter('basePath', $this->basePath);

        $this->addSubscribers();
        $this->registerListeners();
        $this->registerApp();
        $this->registerAppCache();

        $this->request = Request::createFromGlobals();

        try {
            $this->createDotenv()->safeLoad();
        } catch (InvalidFileException $e) {
            $this->writeErrorAndDie($e);
        }

        return $this;
    }

    function registerListeners(): void
    {
        $this->container->register('listener.router', RouterListener::class)->setArguments([new Reference('matcher'), new Reference('request_stack')]);
        $this->container->register('listener.response', ResponseListener::class)->setArguments(['UTF-8']);
        $this->container->register('listener.exception', SymfonyErrorListener::class)->setArguments(['Barker\Events\Listeners\ErrorListener::exception']);
        $this->container->register('listener.error', SymfonyErrorListener::class)->setArguments(['Barker\Events\Listeners\ErrorListener::error']);
        $this->container->register('listener.string_response', StringResponseListener::class);
        $this->container->register('listener.google', GoogleListener::class);
    }

    function addSubscribers(): void
    {
        $this->dispatcher->addMethodCall('addSubscriber', [new Reference('listener.router')]);
        $this->dispatcher->addMethodCall('addSubscriber', [new Reference('listener.response')]);
        $this->dispatcher->addMethodCall('addSubscriber', [new Reference('listener.exception')]);
        $this->dispatcher->addMethodCall('addSubscriber', [new Reference('listener.string_response')]);
        $this->dispatcher->addMethodCall('addSubscriber', [new Reference('listener.google')]);
    }

    function registerApp(): void
    {
        $this->container->register('app', HttpKernel::class)
            ->setArguments([
                new Reference('dispatcher'),
                new Reference('controller_resolver'),
                new Reference('request_stack'),
                new Reference('argument_resolver'),
            ]);
    }

    function registerAppCache(): void
    {
        $this->container->register('store', Store::class)->setArguments(["$this->basePath/cache"]);
        $this->container->register('esi', Esi::class);

        $this->container->register('appCache', HttpCache::class)
            ->setArguments([
                new Reference('app'),
                new Reference('store'),
                new Reference('esi'),
            ]);
    }

    protected function createDotenv(): Dotenv
    {
        Env::enablePutenv();
        return Dotenv::create(Env::getRepository(), $this->basePath);
    }

    public function getResponse(): Response
    {
        return $this->container->get('appCache')->handle($this->request);
    }

    protected function env(string $name): string
    {
        return Env::get($name);
    }

    #[NoReturn] protected function writeErrorAndDie(InvalidFileException $e): void
    {
        $output = (new ConsoleOutput)->getErrorOutput();

        $output->writeln('The environment file is invalid!');
        $output->writeln($e->getMessage());

        http_response_code(500);

        exit(1);
    }
}