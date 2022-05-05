<?php

namespace CodeBuds\SyliusFCMPlugin\Command;

use CodeBuds\SyliusFCMPlugin\Service\FirebaseMessaging;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FcmGetUserTopicSubscriptionsCommand extends Command
{
    use GetUserTrait;

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

        $table = new Table($output);
        $table
            ->setHeaders(['Token', 'Topic']);


        $count = 0;
        foreach ($subscriptions as $subscription) {
            $count++;
            $token = mb_strimwidth($subscription->registrationToken(), 0, 10, "...");
            $table->addRow([$token, $subscription->topic()]);
        }

        $table->addRows([
            new TableSeparator(),
            [new TableCell('Total'), new TableCell($count)]
        ]);

        $table->render();



        return Command::SUCCESS;
    }

}
