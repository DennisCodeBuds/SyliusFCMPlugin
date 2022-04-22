<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity;

class FCMConfiguration implements FCMConfigurationInterface
{
    use TimestampableEntity;

    private $id;

    private $key;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }
}
