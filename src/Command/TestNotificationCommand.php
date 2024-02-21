<?php

namespace App\Command;

use App\Service\Notifications\NotificationsService;
use App\Service\OneSignalService;
use onesignal\client\ApiException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestNotificationCommand extends Command
{
    private NotificationsService $notifications;

    public function __construct(NotificationsService $notifications)
    {
        parent::__construct();
        $this->notifications = $notifications;
    }

    protected function configure(): void
    {
        $this->setName('notification:test');
    }

    /**
     * @throws ApiException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->notifications->sendNotification(['63333534-6162-3066-2d31-3131302d3462'], 'Hello, world!', 'Testq');

        return Command::SUCCESS;
    }
}
