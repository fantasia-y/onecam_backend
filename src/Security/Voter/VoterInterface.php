<?php

namespace App\Security\Voter;

use App\Entity\Auth\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('security.entity_voter')]
interface VoterInterface
{
    public function supports(): array;

    public function hasReadAccess($subject, ?User $user): bool;

    public function hasCreateAccess($subject, ?User $user): bool;

    public function hasUpdateAccess($subject, ?User $user): bool;

    public function hasDeleteAccess($subject, ?User $user): bool;
}