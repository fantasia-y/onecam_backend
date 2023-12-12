<?php

namespace App\Service\Session;

use App\Entity\Session\Session;
use App\Repository\Session\SessionRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Uid\Uuid;

class SessionService
{
    private Security $security;
    private SessionRepository $sessionRepository;

    public function __construct(Security $security, SessionRepository $sessionRepository)
    {
        $this->security = $security;
        $this->sessionRepository = $sessionRepository;
    }

    public function createSession(): Session
    {
        $session = new Session();

        $user = $this->security->getUser();
        $uuid = Uuid::v4();

        $session
            ->setOwner($user)
            ->setSessionId($uuid);

        return $session;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function joinSession(string $id): void
    {
        $session = $this->sessionRepository->findBySessionId($id);

        $session->addParticipant($this->security->getUser());

        $this->sessionRepository->save($session);
    }
}