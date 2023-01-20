<?php

namespace CodeBuds\SyliusFCMPlugin\Command;

use App\Entity\User\ShopUser;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMNotification;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopic;
use CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FcmAddNotification extends Command
{
    protected static $defaultName = 'fcm:notification:add';
    protected static $defaultDescription = 'Add a notification for a topic';
    private EntityManagerInterface $manager;

    public function __construct(
        EntityManagerInterface $manager,
    )
    {
        parent::__construct();
        $this->manager = $manager;
    }


    protected function configure(): void
    {
        $this
            ->addOption('topic', null, InputOption::VALUE_REQUIRED, 'The shop user to set a token for')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'The title for the notification')
            ->addOption('body', null, InputOption::VALUE_OPTIONAL, 'The body for the notification');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $topicRepository = $this->manager->getRepository(ProductFCMTopic::class);

        $topicValue = $input->getOption('topic');

        $topic = $topicRepository->findOneBy(['topicId' => $topicValue]);

        if (!$topic) {
            $io->error(sprintf('No topic found for %s', $topic));
            return Command::FAILURE;
        }

        $titleValue = $input->getOption('title') ?? 'title';
        $bodyValue = $input->getOption('body') ?? 'body';

        $token = (new ProductFCMNotification())
            ->setTopic($topic)
            ->setTitle($titleValue)
            ->setBody($bodyValue)
        ;

        $this->manager->persist($token);
        $this->manager->flush();

        return Command::SUCCESS;
    }
}
