<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

interface TopicSubscriptionInterface
{
    public function getId(): ?int;

    public function getTopic(): FCMTopicInterface;

    public function setTopic(FCMTopicInterface $topic): TopicSubscriptionInterface;

    public function getToken(): FCMTokenInterface;

    public function setToken(FCMTokenInterface $token): TopicSubscriptionInterface;
}
