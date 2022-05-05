<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

interface FCMTokenOwnerInterface
{
    public function getFcmTokens();

    public function addFcmToken(FCMTokenInterface $fcmToken): self;

    public function removeFcmToken(FCMTokenInterface $fcmToken): self;

    public function hasToken(string $fcmTokenValue): bool;
}
