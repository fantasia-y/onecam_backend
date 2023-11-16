<?php

namespace App\Security\Voter;

use App\Entity\Auth\User;
use App\Entity\Session\Session;

class SessionVoter implements VoterInterface
{
    public function supports(): array
    {
        return [
            Session::class
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
        return $subject->getOwner()->getId() === $user->getId();
    }

    public function hasDeleteAccess($subject, ?User $user): bool
    {
        return $this->hasCreateAccess($subject, $user);
    }
}