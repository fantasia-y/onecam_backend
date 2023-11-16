<?php

namespace App\EventSubscriber;

use App\Entity\Auth\RefreshToken;
use App\Entity\Auth\User;
use App\Security\VoterResolver;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class DoctrineEntitySubscriber implements EventSubscriber
{
    private const MODE_READ = 1;
    private const MODE_UPDATE = 2;
    private const MODE_CREATE = 3;
    private const MODE_DELETE = 4;

    private VoterResolver $resolver;
    private Security $security;

    public function __construct(
        VoterResolver $resolver,
        Security $security
    ){
        $this->resolver = $resolver;
        $this->security = $security;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
            Events::onFlush,
        ];
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $this->checkAccess($args->getObject(), self::MODE_READ);
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $unitOfWork = $args->getObjectManager()->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $subject) {
            $this->checkAccess($subject, self::MODE_CREATE);
        }
        foreach ($unitOfWork->getScheduledEntityUpdates() as $subject) {
            $this->checkAccess($subject, self::MODE_UPDATE);
        }
        foreach ($unitOfWork->getScheduledEntityDeletions() as $subject) {
            $this->checkAccess($subject, self::MODE_DELETE);
        }
        foreach ($unitOfWork->getScheduledCollectionUpdates() as $subject) {
            foreach ($subject as $child) {
                $this->checkAccess($child, self::MODE_UPDATE);
            }
        }
        foreach ($unitOfWork->getScheduledCollectionDeletions() as $subject) {
            foreach ($subject as $child) {
                $this->checkAccess($child, self::MODE_DELETE);
            }
        }
    }

    private function checkAccess($entity, int $mode): void
    {
        $class = ClassUtils::getRealClass(get_class($entity));
        $user = $this->security->getUser();

        // user is null when jwt is checked, otherwise access denied
        if ($user === null && !in_array($class, [User::class, RefreshToken::class])) {
            throw new AccessDeniedException('Access Denied.');
        }

        $voters = $this->resolver->resolve($class);

        foreach ($voters as $voter) {
            $hasAccess = match ($mode) {
                self::MODE_READ => $voter->hasReadAccess($entity, $user),
                self::MODE_UPDATE => $voter->hasUpdateAccess($entity, $user),
                self::MODE_CREATE => $voter->hasCreateAccess($entity, $user),
                self::MODE_DELETE => $voter->hasDeleteAccess($entity, $user),
            };

            if (!$hasAccess) {
                throw new AccessDeniedException("Access Denied for entity $class");
            }
        }
    }
}