<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestTransformerSubscriber
{
    /**
     * @throws \JsonException
     */
    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getContentTypeFormat() === 'json' && $request->getContent()) {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $request->request->replace($data);
        }
    }
}