<?php

namespace App\Service\Session;

use App\Entity\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Uid\Uuid;

class SessionService
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function createSession(): Session
    {
        $session = new Session();

        $user = $this->tokenStorage->getToken()->getUser();
        $uuid = Uuid::v4();

        $session
            ->setOwner($user)
            ->setSessionId($uuid);

        return $session;
    }
}