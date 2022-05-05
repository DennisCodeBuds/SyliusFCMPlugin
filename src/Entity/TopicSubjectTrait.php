<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

trait TopicSubjectTrait
{
    private $topic;

    public function getTopic(): ?FCMTopicInterface
    {
        return $this->topic;
    }

    public function setTopic(?FCMTopicInterface $topic): self
    {
        $this->topic = $topic;
        return $this;
    }
}
