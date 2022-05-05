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

class FcmUnsubscribeUserFromTopicsCommand extends Command
{
    use GetUserTrait;

    protected static $defaultName = 'fcm:topic-subscriptions:unsubscribe';
    protected static $defaultDescription = 'Unsubscribe a user from a topic or from all topics';
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
            ->addOption('all', null, InputOption::VALUE_NONE, 'The user will be unsubscribed from all topics')
            ->addOption('topic', null, InputOption::VALUE_OPTIONAL, 'The topic id to unsubscribe from')
            ->addOption('no-local-check', null, InputOption::VALUE_NONE, 'The topic must exist in the local database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = $this->getUser($input, $io);

        if (!$user) {
            return Command::FAILURE;
        }

        if ($input->getOption('all')) {
            $this->topicService->unsubscribeUserFromAllTopics($user);
            return Command::SUCCESS;
        }

        if ($topicId = $input->getOption('topic')) {
            $topicRepository = $this->manager->getRepository(FCMEntityTopic::class);
            $topic = $topicRepository->findOneBy(['topicId' => $topicId]);
            if (!$input->getOption('no-local-check') && !$topic) {
                $io->error(sprintf('No topic found with the topicId of %s', $topicId));
                return Command::FAILURE;
            }

            if($topic) {
                $this->topicService->unsubscribeUserFromTopic($user, $topic);
            } else {
                $this->topicService->unsubscribeUserFromTopicId($user, $topicId);
            }
        }

        return Command::SUCCESS;
    }
}
