<?php

namespace App\Security\Voter;

use App\Entity\Auth\User;
use App\Entity\Group\Group;
use App\Entity\Group\GroupImage;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupVoter implements VoterInterface
{
    public function supports(): array
    {
        return [
            Group::class,
            GroupImage::class
        ];
    }

    public function hasReadAccess($subject, ?UserInterface $user): bool
    {
        return match (get_class($subject)) {
            GroupImage::class => $subject->getGroup()->isMember($user),
            default => true,
        };

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