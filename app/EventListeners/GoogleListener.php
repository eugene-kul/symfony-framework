<?php

namespace App\EventListeners;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class GoogleListener implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (
            ($response->headers->has('Skip-Google') && $response->headers->get('Skip-Google') === 'yes') ||
            $response->isRedirection()
            || ($response->headers->has('Content-Type') && !str_contains($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $event->getRequest()->getRequestFormat()
        ) {
            $response->headers->remove('Skip-Google');

            return;
        }

        $response->headers->remove('Skip-Google');
        $response->setContent($response->getContent().'<br><hr> GA CODE - ' . rand());
    }

    public static function getSubscribedEvents(): array
    {
        return ['kernel.response' => 'onKernelResponse'];
    }
}