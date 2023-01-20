<?php

namespace CodeBuds\SyliusFCMPlugin\Command;

use App\Entity\User\ShopUser;
use CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FcmAddUserToken extends Command
{
    protected static $defaultName = 'fcm:token:add';
    protected static $defaultDescription = 'Add a token for a user';
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
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'The shop user to set a token for')
            ->addOption('token', null, InputOption::VALUE_OPTIONAL, 'The token to set for the user, if not set a random token will be generated');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userRepository = $this->manager->getRepository(ShopUser::class);

        $username = $input->getOption('username');

        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $io->error(sprintf('No user found for username %s', $username));
            return Command::FAILURE;
        }

        $tokenValue = $input->getOption('token') ?? 'token';

        $token = (new ShopUserFCMToken())
            ->setShopUser($user)
            ->setValue($tokenValue);

        $this->manager->persist($token);
        $this->manager->flush();

        return Command::SUCCESS;
    }
}
