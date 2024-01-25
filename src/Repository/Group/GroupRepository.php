<?php

namespace App\Repository\Group;

use App\Entity\Auth\User;
use App\Entity\Group\Group;
use App\Repository\BaseRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV4;

class GroupRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function findByGroupId(string $id): ?Group
    {
        $uuid = UuidV4::fromString($id);

        $qb = $this->createQueryBuilder('g')
            ->where('g.groupId = :group_id')
            ->setParameter('group_id', $uuid->toBinary());

        try {
            $result = $qb->getQuery()->getOneOrNullResult();

            if ($result === null) {
                throw $this->createNotFoundException();
            }

            return $result;
        } catch (NonUniqueResultException $exception) {
            throw $this->createNotFoundException();
        }
    }

    /**
     * @return Group[]
     */
    public function getAllByUser(UserInterface $user): array
    {
        $qb = $this->createQueryBuilder('g')
            ->where('g.owner = :user')
            ->orWhere(':user MEMBER OF g.participants')
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }
}