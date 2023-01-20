<?php

namespace CodeBuds\SyliusFCMPlugin\Command;

use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopic;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTopic;
use CodeBuds\SyliusFCMPlugin\Service\FCMTopicService;
use CodeBuds\SyliusFCMPlugin\Service\FirebaseMessaging;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FcmDeleteTopicCommand extends Command
{
    protected static $defaultName = 'fcm:topic:delete';
    protected static $defaultDescription = 'Delete a topic';
    private EntityManagerInterface $manager;
    private FCMTopicService $topicService;

    public function __construct(
        EntityManagerInterface $manager,
        FCMTopicService        $topicService
    )
    {
        parent::__construct();
        $this->manager = $manager;
        $this->topicService = $topicService;
    }


    protected function configure(): void
    {
        $this
            ->addOption('topic', null, InputOption::VALUE_OPTIONAL, 'The topic id to delete')
            ->addOption('no-local-check', null, InputOption::VALUE_NONE, 'The topic must exist in the local database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($topicId = $input->getOption('topic')) {
            $topicRepository = $this->manager->getRepository(FCMEntityTopic::class);
            /** @var FCMTopic $topic */
            $topic = $topicRepository->findOneBy(['id' => $topicId]);
            if (!$input->getOption('no-local-check') && !$topic) {
                $io->error(sprintf('No topic found with the topicId of %s', $topicId));
                return Command::FAILURE;
            }

            if ($topic) {
                $this->topicService->deleteTopic($topic);
            } else {
                $io->error(sprintf('Topic %s was not found', $topic->getTopicId()));
            }
            $this->manager->flush();
        }

        return Command::SUCCESS;
    }
}
