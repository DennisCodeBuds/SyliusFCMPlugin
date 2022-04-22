<?php

namespace CodeBuds\SyliusFCMPlugin\Event;

use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription;
use CodeBuds\SyliusFCMPlugin\Entity\TopicSubscriptionInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TopicSubscriptionEvent extends Event
{
    public const NAME = 'fcm.topic.subscription';

    protected $data;

    /**
     * @var TopicSubscriptionInterface
     */
    protected TopicSubscriptionInterface $topicSubscription;

    /**
     * @var FCMTokenInterface[]
     */
    protected $tokens;

    protected int $subscriptionCount;

    public function __construct(TopicSubscription $data)
    {
        $this->data = $data;
    }

    public function getData(): TopicSubscription
    {
        return $this->data;
    }

    public function getTopicSubscription(): TopicSubscriptionInterface
    {
        return $this->topicSubscription;
    }

    public function setTopicSubscription(TopicSubscriptionInterface $topicSubscription): self
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
