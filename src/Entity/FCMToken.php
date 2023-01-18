<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Sylius\Component\Resource\Model\ResourceInterface;

abstract class FCMToken implements ResourceInterface, FCMTokenInterface
{
    use TimestampableEntity;

    protected $id;

    protected $owner;

    protected $value;

    protected $subscriptions;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?FCMTokenOwnerInterface
    {
        return $this->owner;
    }

    public function setOwner(?FCMTokenOwnerInterface $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return TopicSubscriptionInterface[]|null
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
