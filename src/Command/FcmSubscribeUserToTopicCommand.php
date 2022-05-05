<?php

namespace CodeBuds\SyliusFCMPlugin\Command;

use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopic;
use CodeBuds\SyliusFCMPlugin\Service\FCMTopicService;
use CodeBuds\SyliusFCMPlugin\Service\FirebaseMessaging;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FcmSubscribeUserToTopicCommand extends Command
{
    use GetUserTrait;

    protected static $defaultName = 'fcm:topic-subscriptions:subscribe';
    protected static $defaultDescription = 'Subscribe a user to a topic';
    private EntityManagerInterface $manager;
    private FirebaseMessaging $messaging;
    private FCMTopicService $topicService;

    public function __construct(
        EntityManagerInterface $manager,
        FirebaseMessaging      $messaging,
        FCMTopicService        $topicService
    )
    {
        parent::__construct();
        $this->manager = $manager;
        $this->messaging = $messaging;
        $this->topicService = $topicService;
    }


    protected function configure(): void
    {
        $this
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'The shopUser username')
            ->addOption('topic', null, InputOption::VALUE_OPTIONAL, 'The topic id to subscribe to')
            ->addOption('no-local-check', null, InputOption::VALUE_NONE, 'The topic must exist in the local database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = $this->getUser($input, $io);

        if (!$user) {
            return Command::FAILURE;
        }

        if ($topicId = $input->getOption('topic')) {
            $topicRepository = $this->manager->getRepository(FCMEntityTopic::class);
            $topic = $topicRepository->findOneBy(['topicId' => $topicId]);
            if (!$input->getOption('no-local-check') && !$topic) {
                $io->error(sprintf('No topic found with the topicId of %s', $topicId));
                return Command::FAILURE;
            }

            if ($topic) {
                $this->topicService->generateSubscriptionsForUser($user, $topic);
            } else {
                $this->topicService->generateSubscriptionsForUserToTopicId($user, $topicId);
            }
            $this->manager->flush();
        }

        return Command::SUCCESS;
    }
}
