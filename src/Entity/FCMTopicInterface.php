<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

interface FCMTopicInterface
{
    public function setTopicId(string $topicId): self;

    public function getTopicId(): string;

    public function isSubscribed(): bool;

    public function setSubscribed(bool $subscribed): self;

    public function getSubscriptions();
}
