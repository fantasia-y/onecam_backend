<?php

namespace App\Entity\Auth;

use App\Repository\Auth\NotificationSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationSettingsRepository::class)]
class NotificationSettings
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $newImageNotifications = true;

    #[ORM\Column]
    private ?bool $newMemberNotifications = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNewImageNotifications(): ?bool
    {
        return $this->newImageNotifications;
    }

    public function setNewImageNotifications(?bool $newImageNotifications): NotificationSettings
    {
        $this->newImageNotifications = $newImageNotifications;
        return $this;
    }

    public function getNewMemberNotifications(): ?bool
    {
        return $this->newMemberNotifications;
    }

    public function setNewMemberNotifications(?bool $newMemberNotifications): NotificationSettings
    {
        $this->newMemberNotifications = $newMemberNotifications;
        return $this;
    }
}
