<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Doctrine\Common\Collections\Collection;

interface FCMTokenOwnerInterface
{
    public function getFcmTokens(): ?Collection;

    public function addFcmToken(FCMToken $fcmToken): self;

    public function removeFcmToken(FCMToken $fcmToken): self;

    public function hasToken(string $fcmTokenValue): bool;
}
