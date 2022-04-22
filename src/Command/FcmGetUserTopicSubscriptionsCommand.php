<?php

namespace CodeBuds\SyliusFCMPlugin\Command;

use CodeBuds\SyliusFCMPlugin\Service\FirebaseMessaging;
use Doctrine\ORM\EntityManagerInterface;
use getUserTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FcmGetUserTopicSubscriptionsCommand extends Command
{
    use getUserTrait;

    protected static $defaultName = 'fcm:topic-subscriptions:get';
    protected static $defaultDescription = 'Get a list of the topic subscriptions for a user';
    private EntityManagerInterface $manager;
    private FirebaseMessaging $messaging;

    public function __construct(
        EntityManagerInterface $manager,
        FirebaseMessaging      $messaging
    )
    {
        parent::__construct();
        $this->manager = $manager;
        $this->messaging = $messaging;
    }


    protected function configure(): void
    {
        $this
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'The shopUser username');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = $this->getUser($input, $io);

        if (!$user) {
            return Command::FAILURE;
        }

        $subscriptions = $this->messaging->getTopicSubscriptions($user);

        foreach ($subscriptions as $subscription) {
            $io->comment("{$subscription->registrationToken()} is subscribed to {$subscription->topic()}\n");
        }

        return Command::SUCCESS;
    }

}
