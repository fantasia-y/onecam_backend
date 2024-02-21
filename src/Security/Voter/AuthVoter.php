<?php

namespace App\Security\Voter;

use App\Entity\Auth\NotificationSettings;
use App\Entity\Auth\RefreshToken;
use App\Entity\Auth\User;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthVoter implements VoterInterface
{

    public function supports(): array
    {
        return [
            User::class,
            RefreshToken::class,
            NotificationSettings::class
        ];
    }

    public function hasReadAccess($subject, ?UserInterface $user): bool
    {
        return true;
    }

    public function hasCreateAccess($subject, ?UserInterface $user): bool
    {
        return true;
    }

    public function hasUpdateAccess($subject, ?UserInterface $user): bool
    {
        $class = ClassUtils::getRealClass(get_class($subject));

        return match ($class) {
            User::class => $subject->getId() === $user->getId(),
            RefreshToken::class => true,
            NotificationSettings::class => true,
        };
    }

    public function hasDeleteAccess($subject, ?UserInterface $user): bool
    {
        return $this->hasUpdateAccess($subject, $user);
    }

    public function hasAnonReadAccess($subject): bool
    {
        return true;
    }

    public function hasAnonCreateAccess($subject): bool
    {
        return true;
    }

    public function hasAnonUpdateAccess($subject): bool
    {
        return false;
    }

    public function hasAnonDeleteAccess($subject): bool
    {
        return false;
    }
}
