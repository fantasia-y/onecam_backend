<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;

abstract class BaseRepository extends ServiceEntityRepository
{
    /**
     * @throws EntityNotFoundException
     */
    public function existsOrThrowNotFound($id): void
    {
        if (!empty($id)) {
            $entity = $this->find($id);
            if (!empty($entity)) {
                return;
            }
        }
        throw new EntityNotFoundException();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function findOrThrowNotFound($id)
    {
        if (!empty($id)) {
            $entity = $this->find($id);
            if (!empty($entity)) {
                return $entity;
            }
        }
        throw new EntityNotFoundException();
    }

    public function save($entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove($entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeAll(array $entities, bool $flush = true): void
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->remove($entity);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}