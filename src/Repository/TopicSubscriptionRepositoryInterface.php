<?php

namespace CodeBuds\SyliusFCMPlugin\Repository;

use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTopicInterface;

interface TopicSubscriptionRepositoryInterface
{
    public function getSubscribedTopics(FCMTokenOwnerInterface $user, ?string $type);

    public function getSubscribedTopic(FCMTokenOwnerInterface $user, FCMTopicInterface $topic);
}
