<?php

namespace Barker\Main;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class App extends HttpKernel
{
    public function __construct(...$arguments)
    {
        parent::__construct(...$arguments);


    }
//
//    public function handle(
//        Request $request,
//        int $type = HttpKernelInterface::MAIN_REQUEST,
//        bool $catch = true
//    ): Response
//    {
//        $this->matcher->getContext()->fromRequest($request);
//
//        try {
//            $request->attributes->add($this->matcher->match($request->getPathInfo()));
//
//            $controller = $this->controllerResolver->getController($request);
//            $arguments = $this->argumentResolver->getArguments($request, $controller);
//
//            $response = call_user_func_array($controller, $arguments);
//        } catch (ResourceNotFoundException $exception) {
//            $response = new Response('Not Found', 404);
//        } catch (\Exception $exception) {
//            $response = new Response('An error occurred', 500);
//        }
//
//        // dispatch a response event
//        $this->dispatcher->dispatch(new ResponseEvent($response, $request), 'response');
//
//        return $response;
//    }
}