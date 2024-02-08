<?php

namespace App\Repository\Group;

use App\Entity\Auth\User;
use App\Entity\Group\Group;
use App\Entity\Group\GroupImage;
use App\Repository\BaseRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\UuidV4;

class GroupImageRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupImage::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getGroupImageCount(Group $group): int
    {
        $qb = $this->createQueryBuilder('gi')
            ->select('COUNT(gi)')
            ->where('gi.group = :group')
            ->setParameter('group', $group);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getGroupImages(Group $group, int $page = 0, int $size = 30): array
    {
        $qb = $this->createQueryBuilder('gi')
            ->select('gi, g')
            ->leftJoin('gi.group', 'g')
            ->where('gi.group = :group')
            ->setParameter('group', $group)
            ->orderBy('gi.id', Criteria::DESC)
            ->setFirstResult($page * $size)
            ->setMaxResults($size);

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function existsInGroup(GroupImage $image, Group $group): bool
    {
        $qb = $this->createQueryBuilder('gi')
            ->select('COUNT(gi)')
            ->where('gi.group = :group')
            ->andWhere('gi.name = :name')
            ->setParameters([
                'group' => $group,
                'name' => $image->getImageName()
            ]);

        return $qb->getQuery()->getSingleScalarResult() === 1;
    }
}