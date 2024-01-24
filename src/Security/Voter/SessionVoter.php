<?php

namespace App\Security\Voter;

use App\Entity\Auth\User;
use App\Entity\Group\Group;
use Symfony\Component\Security\Core\User\UserInterface;

class SessionVoter implements VoterInterface
{
    public function supports(): array
    {
        return [
            Group::class
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
        // TODO differentiate between update and inserting into collection
        return true;
    }

    public function hasDeleteAccess($subject, ?UserInterface $user): bool
    {
        return $this->hasCreateAccess($subject, $user);
    }

    public function hasAnonReadAccess($subject): bool
    {
        return true;
    }

    public function hasAnonCreateAccess($subject): bool
    {
        return false;
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