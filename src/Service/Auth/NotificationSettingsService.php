<?php

namespace App\Service\Auth;

use App\Entity\Auth\User;
use App\Repository\Auth\NotificationSettingsRepository;
use Symfony\Bundle\SecurityBundle\Security;

class NotificationSettingsService
{
    private NotificationSettingsRepository $settingsRepository;
    private Security $security;

    public function __construct(NotificationSettingsRepository $settingsRepository, Security $security)
    {
        $this->settingsRepository = $settingsRepository;
        $this->security = $security;
    }

    public function updateNotificationSettings(array $settings): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $notificationSettings = $user->getNotificationSettings();

        foreach ($settings as $setting => $value) {
            $notificationSettings->{"set$setting"}(boolval($value));
        }

        $this->settingsRepository->save($notificationSettings);
    }
}
