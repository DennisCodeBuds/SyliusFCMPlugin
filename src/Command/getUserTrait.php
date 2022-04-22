<?php

use App\Entity\User\ShopUser;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;

trait getUserTrait
{
    protected function getUser($input, $io): FCMTokenOwnerInterface
    {
        $username = $input->getOption('username');
        $userRepository = $this->manager->getRepository(ShopUser::class);
        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $io->error(sprintf('No shop user found with the username %s', $username));
        }

        return $user;
    }
}
