<?php

namespace App\Controller;

use App\Entity\Auth\User;
use App\Service\OneSignalService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class BaseController extends AbstractController
{
    public const DEFAULT_GROUPS = [
        'Default',
    ];

    protected SerializerInterface $serializer;
    protected OneSignalService $oneSignalService;
    private RequestStack $requestStack;

    public function __construct(
        SerializerInterface $serializer,
        OneSignalService $oneSignalService,
        RequestStack $requestStack
    ) {
        $this->serializer = $serializer;
        $this->oneSignalService = $oneSignalService;
        $this->requestStack = $requestStack;
    }

    /**
     * @return User|null
     */
    protected function getUser(): ?UserInterface
    {
        return parent::getUser();
    }

    public function parseDate(string $key): ?\DateTime
    {
        $date = \DateTime::createFromFormat('Y-m-d', $this->requestStack->getCurrentRequest()->get($key));
        return !$date ? null : $date->setTime(0, 0);
    }

    public function jsonResponse($data, array $groups = ['Default'], int $status = 200): Response
    {
        $context = new SerializationContext();
        $context->enableMaxDepthChecks();
        $context->setGroups($groups);

        $headers = ['Content-Type' => 'application/json'];
        return new Response($this->serializer->serialize($data, 'json', $context), $status, $headers);
    }
}