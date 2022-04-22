<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Doctrine\Common\Collections\Collection;

interface FCMRecipientInterface extends FCMTokenOwnerInterface
{
    public function getNotifications(): ?Collection;

    public function addNotification(FCMNotificationInterface $message): self;

    public function removeNotification(FCMNotificationInterface $message): self;
}
