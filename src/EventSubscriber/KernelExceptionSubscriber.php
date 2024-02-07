<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityNotFoundException;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelExceptionSubscriber
{
    private string $environment;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;

    public function __construct(
        string $environment,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->environment = $environment;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onException(ExceptionEvent $event): void
    {
        $this->logger->error($event->getThrowable()->getMessage());

        $exception = $event->getThrowable();

        $status = match (get_class($exception)) {
            HttpExceptionInterface::class => $exception->getStatusCode(),
            EntityNotFoundException::class => Response::HTTP_NOT_FOUND,
            default => Response::HTTP_INTERNAL_SERVER_ERROR
        };
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