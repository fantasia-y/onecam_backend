<?php

namespace App\EventSubscriber;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelExceptionSubscriber
{
    private string $environment;
    private SerializerInterface $serializer;

    public function __construct(
        string $environment,
        SerializerInterface $serializer
    ) {
        $this->environment = $environment;
        $this->serializer = $serializer;
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = $exception->getMessage();

        $data = [
            'code' => $status,
            'message' => $this->environment === 'dev' ? $message : '',
        ];

        $headers = ['Content-Type' => 'application/json'];
        $response = new Response($this->serializer->serialize($data, 'json'), $status, $headers);

        $event->setResponse($response);
    }
}