<?php

namespace CodeBuds\SyliusFCMPlugin\Event;

use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopic;
use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopicInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TopicSubscriptionToggleEvent extends Event
{
    public const NAME = 'fcm.topic.subscription.toggle';

    protected $data;

    protected bool $subscribed;

    public function __construct(FCMEntityTopicInterface $data)
    {
        $this->data = $data;
    }

    public function getData(): FCMEntityTopicInterface
    {
        return $this->data;
    }

    public function isSubscribed(): bool
    {
        return $this->subscribed;
    }

    public function setSubscribed(bool $subscribed): TopicSubscriptionToggleEvent
    {
        $this->subscribed = $subscribed;
        return $this;
    }


}
