<?php

namespace App\Command;

use App\Service\OneSignalService;
use onesignal\client\ApiException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestNotificationCommand extends Command
{
    private OneSignalService $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        parent::__construct();
        $this->oneSignalService = $oneSignalService;
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
        $this->oneSignalService->sendNotification('Test Notification', 'This is a test', [
            '1EDDAD96-3044-6960-BD64-411746E51C4E',
            '1EDDA3DF-B015-688A-B170-D5AC88E93BF5'
        ]);

        return Command::SUCCESS;
    }
}