<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Sylius\Component\Resource\Model\ResourceInterface;

abstract class FCMTopic implements ResourceInterface, FCMTopicInterface
{
    use TimestampableEntity;

    protected $id;

    protected string $topicId;

    protected $subscriptions;

    protected bool $subscribed;

    protected string $toggleRoute;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopicId(): string
    {
        return $this->topicId;
    }

    public function setTopicId(string $topicId): self
    {
        $this->topicId = $topicId;
        return $this;
    }

    /**
     * @return TopicSubscriptionInterface[]|null
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    public function isSubscribed(): bool
    {
        return $this->subscribed;
    }

    public function setSubscribed(bool $subscribed): self
    {
        $this->subscribed = $subscribed;
        return $this;
    }

    public function getToggleRoute(): string
    {
        return $this->toggleRoute;
    }

    public function setToggleRoute(string $toggleRoute): FCMTopic
    {
        $this->toggleRoute = $toggleRoute;
        return $this;
    }


}
