<?php

namespace CodeBuds\SyliusFCMPlugin\Service;

use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Security;

class FCMTokenService
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return FCMTokenInterface[]|null
     * @throws \Exception
     */
    public function getCurrentUserTokens(): ?Collection
    {
        $user = $this->security->getUser();
        if (!($user instanceof FCMTokenOwnerInterface)) {
            throw new \Exception('The current user does not own FCM Tokens');
        }

        return $user->getFcmTokens();
    }

    public function getCurrentUserTokenValues(): array
    {
        $user = $this->security->getUser();
        if (!($user instanceof FCMTokenOwnerInterface)) {
            throw new \Exception('The current user does not own FCM Tokens');
        }

        return $this->getTokenValues($user->getFcmTokens());
    }

    /**
     * @param FCMTokenInterface[] $tokens
     */
    public function getTokenValues($tokens): array
    {
        return array_map(static fn(FCMTokenInterface $token) => $token->getValue(), $tokens->toArray());
    }
}
