<?php

namespace CodeBuds\SyliusFCMPlugin\Event;

use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopic;
use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopicInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TopicSubscribedEvent extends Event
{
    public const NAME = 'fcm.topic.subscribed';

    protected $data;

    /**
     * @var FCMEntityTopic
     */
    protected FCMEntityTopicInterface $topic;

    /**
     * @var FCMTokenInterface[]
     */
    protected $tokens;

    protected int $subscriptionCount;

    public function __construct(FCMEntityTopicInterface $data)
    {
        $this->data = $data;
    }

    public function getData(): FCMEntityTopicInterface
    {
        return $this->data;
    }

    public function getTopicSubscription(): FCMEntityTopicInterface
    {
        return $this->topicSubscription;
    }

    public function setTopicSubscription(FCMEntityTopicInterface $topicSubscription): self
    {
        $this->topicSubscription = $topicSubscription;
        return $this;
    }

    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @param FCMTokenInterface[] $tokens
     * @return $this
     */
    public function setTokens($tokens): self
    {
        $this->tokens = $tokens;
        return $this;
    }

    public function getSubscriptionCount(): int
    {
        return $this->subscriptionCount;
    }

    public function setSubscriptionCount(int $subscriptionCount): self
    {
        $this->subscriptionCount = $subscriptionCount;
        return $this;
    }
}
