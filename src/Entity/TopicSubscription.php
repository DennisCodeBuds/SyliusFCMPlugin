<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class TopicSubscription implements ResourceInterface, TopicSubscriptionInterface
{
    protected $id;

    /** @var FCMTopicInterface */
    protected $topic;

    /** @var FCMTokenInterface */
    protected $token;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopic(): FCMTopicInterface
    {
        return $this->topic;
    }

    public function setTopic(FCMTopicInterface $topic): TopicSubscriptionInterface
    {
        $this->topic = $topic;
        return $this;
    }

    public function getToken(): FCMTokenInterface
    {
        return $this->token;
    }

    public function setToken(FCMTokenInterface $token): TopicSubscriptionInterface
    {
        $this->token = $token;
        return $this;
    }
}
