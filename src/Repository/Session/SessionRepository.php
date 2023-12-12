<?php

namespace App\Repository\Session;

use App\Entity\Auth\User;
use App\Entity\Session\Session;
use App\Repository\BaseRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\UuidV4;

class SessionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function findBySessionId(string $id): ?Session
    {
        $uuid = UuidV4::fromString($id);

        $qb = $this->createQueryBuilder('s')
            ->where('s.sessionId = :session_id')
            ->setParameter('session_id', $uuid->toBinary());

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

    public function getAllByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.owner = :user')
            ->orWhere(':user MEMBER OF s.participants')
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }
}