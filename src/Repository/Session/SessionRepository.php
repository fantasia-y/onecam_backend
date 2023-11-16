<?php

namespace App\Repository\Session;

use App\Entity\Session\Session;
use App\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class SessionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }
}