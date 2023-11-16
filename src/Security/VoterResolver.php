<?php

namespace App\Security;

use App\Security\Voter\VoterInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class VoterResolver
{
    /** @var VoterInterface[] */
    private $voters;

    public function __construct(
        #[TaggedIterator('security.entity_voter')]
        $voters
    ) {
        $this->voters = $voters;
    }

    /**
     * @return VoterInterface[]
     */
    public function resolve(string $class): array
    {
        $supported = [];
        foreach ($this->voters as $voter) {
            if (in_array($class, $voter->supports())) {
                $supported[] = $voter;
            }
        }

        if (count($supported) === 0) {
            throw new AccessDeniedException("No Voter for '$class' registered");
        }

        return $supported;
    }
}