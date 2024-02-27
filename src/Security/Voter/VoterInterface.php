<?php

namespace App\Security\Voter;

use App\Entity\Auth\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\User\UserInterface;

#[AutoconfigureTag('security.entity_voter')]
interface VoterInterface
{
    public function supports(): array;

    public function hasReadAccess($subject, ?UserInterface $user): bool;

    public function hasCreateAccess($subject, ?UserInterface $user): bool;

    public function hasUpdateAccess($subject, ?UserInterface $user): bool;

    public function hasDeleteAccess($subject, ?UserInterface $user): bool;

    public function hasAnonReadAccess($subject): bool;

    public function hasAnonCreateAccess($subject): bool;

    public function hasAnonUpdateAccess($subject): bool;

    public function hasAnonDeleteAccess($subject): bool;
}