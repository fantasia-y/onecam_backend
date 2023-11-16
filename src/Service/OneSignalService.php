<?php

namespace App\Service;

use GuzzleHttp\Client;
use onesignal\client\api\DefaultApi;
use onesignal\client\ApiException;
use onesignal\client\Configuration;
use onesignal\client\model\Notification;
use onesignal\client\model\StringMap;

class OneSignalService
{
    private function getInstance(): DefaultApi
    {
        $config = Configuration::getDefaultConfiguration()
            ->setAppKeyToken('NGU3NmY4NDYtOTY2Zi00Y2Y2LWEyNzUtYTljNzliNDI3OTQ4');

        return new DefaultApi(
            new Client(),
            $config
        );
    }

    private function createNotification(string $contentEn, array $recipients, string $titleEn = 'CoLiving'): Notification
    {
        $heading = new StringMap();
        $heading->setEn($titleEn);

        $content = new StringMap();
        $content->setEn($contentEn);

        return (new Notification())
            ->setAppId('43519f21-cf0e-4bb6-a4a0-30abbaa32a10')
            ->setHeadings($heading)
            ->setContents($content)
            ->setIncludeExternalUserIds($recipients);
    }

    public function sendNotification(string $content, array $recipients): bool
    {
        $client = $this->getInstance();

        $notification = $this->createNotification($content, $recipients);

        try {
            $response = $client->createNotification($notification);
            return $response->getErrors() === null;
        } catch (\Exception $exception) {
            // TODO log exception
            return false;
        }
    }

    public function sendToHome(string $content, Home $home, ?Roommate $exclude = null): bool
    {
        $recipients = [];
        foreach ($home->getRoommates() as $roommate) {
            if ($exclude !== null && $exclude->getId() === $roommate->getId()) {
                continue;
            }
            $recipients[] = $roommate->getUser()->getId();
        }

        return $this->sendNotification($content, $recipients);
    }
}