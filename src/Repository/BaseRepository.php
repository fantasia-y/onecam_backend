<?php

namespace App\Repository;

use App\Utils\ArrayUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function beginTransaction(): void
    {
        $this->getEntityManager()->beginTransaction();
    }

    public function commit(): void
    {
        $this->getEntityManager()->commit();
    }

    public function rollback(): void
    {
        $this->getEntityManager()->rollback();
    }

    protected function createNotFoundException(): EntityNotFoundException
    {
        $class = ArrayUtils::last(explode('\\', $this->getEntityName()));
        return new EntityNotFoundException("$class not found");
    }

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
        throw $this->createNotFoundException();
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
        throw $this->createNotFoundException();
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