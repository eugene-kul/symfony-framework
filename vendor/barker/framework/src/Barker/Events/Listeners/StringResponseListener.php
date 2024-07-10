<?php

namespace Barker\Events\Listeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class StringResponseListener implements EventSubscriberInterface
{
    public function onView(ViewEvent $event): void
    {
        $response = $event->getControllerResult();

        dump($event);

        if (is_string($response)) {
            $event->setResponse(new Response($response));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return ['kernel.view' => 'onView'];
    }
}