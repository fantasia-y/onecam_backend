<?php

namespace App\Security\Voter;

use App\Entity\Auth\RefreshToken;
use App\Entity\Auth\User;
use Doctrine\Common\Util\ClassUtils;

class AuthVoter implements VoterInterface
{

    public function supports(): array
    {
        return [
            User::class,
            RefreshToken::class,
        ];
    }

    public function hasReadAccess($subject, ?User $user): bool
    {
        return true;
    }

    public function hasCreateAccess($subject, ?User $user): bool
    {
        return true;
    }

    public function hasUpdateAccess($subject, ?User $user): bool
    {
        $class = ClassUtils::getRealClass(get_class($subject));

        return match ($class) {
            User::class => $subject->getId()->equals($user->getId()),
            RefreshToken::class => true,
        };
    }

    public function hasDeleteAccess($subject, ?User $user): bool
    {
        return $this->hasUpdateAccess($subject, $user);
    }
}