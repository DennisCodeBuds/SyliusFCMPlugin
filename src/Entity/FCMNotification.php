<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Sylius\Component\Resource\Model\ResourceInterface;

abstract class FCMNotification implements ResourceInterface, FCMNotificationInterface
{
    use TimestampableEntity;

    protected $id;

    protected $title;

    protected $body;

    protected $recipient;

    protected $topic;

    protected $data;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getRecipient(): ?FCMRecipientInterface
    {
        return $this->recipient;
    }

    public function setRecipient(?FCMRecipientInterface $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getTopic(): ?FCMTopicInterface
    {
        return $this->topic;
    }

    public function setTopic(?FCMTopicInterface $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
