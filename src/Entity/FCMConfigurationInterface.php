<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface FCMConfigurationInterface extends ResourceInterface
{
    public function getId(): ?int;
}
