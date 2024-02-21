<?php

namespace App\Service\Notifications;

use App\Entity\Auth\User;
use App\Entity\Group\Group;
use Pusher\PushNotifications\PushNotifications;

class NotificationsService
{
    private PushNotifications $notifications;

    public function __construct(PushNotifications $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * @throws \Exception
     */
    public function sendNewImageNotification(Group $group, User $user): void
    {
        $recipients = $this->filterRecipients($group->getRecipients(), 'NewImageNotifications');
        $message = $user->getDisplayname() . ' added a new image to ' . $group->getName();

        $this->sendNotification($recipients, $message);
    }

    /**
     * @throws \Exception
     */
    public function sendNewMemberNotification(Group $group, User $user): void
    {
        $recipients = $this->filterRecipients($group->getRecipients(), 'NewMemberNotifications');
        $message = $user->getDisplayname() . ' joined ' . $group->getName();

        $this->sendNotification($recipients, $message);
    }

    private function filterRecipients(array $recipients, string $notification): array
    {
        return array_map(
            fn (User $user) => $user->getUuid(),
            array_values(array_filter($recipients, fn (User $user) => $user->getNotificationSettings()->{"get$notification"}()))
        );
    }

    /**
     * @throws \Exception
     */
    public function sendNotification(array $ids, string $message): void
    {
        $request = ['apns' => [
            'aps' => [
                'alert' => [
                    'title' => 'OneCam',
                    'body' => $message
                ],
                'content-available' => 1
            ]
        ]];

        $this->notifications->publishToUsers(
            $ids,
            $request
        );
    }
}
