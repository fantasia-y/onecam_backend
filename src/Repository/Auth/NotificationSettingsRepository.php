<?php

namespace App\Repository\Auth;

use App\Entity\Auth\NotificationSettings;
use App\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationSettingsRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationSettings::class);
    }
}
