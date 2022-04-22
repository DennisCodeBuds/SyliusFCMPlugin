<?php

namespace CodeBuds\SyliusFCMPlugin\Event;

use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopic;
use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopicInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TopicUnsubscribedEvent extends Event
{
    public const NAME = 'fcm.topic.unsubscribed';

    protected $data;

    /**
     * @var FCMEntityTopic
     */
    protected FCMEntityTopicInterface $topic;

    /**
     * @var FCMTokenInterface[]
     */
    protected $tokens;

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
}
