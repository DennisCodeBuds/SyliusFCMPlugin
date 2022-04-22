<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

abstract class FCMEntityTopic extends FCMTopic implements FCMEntityTopicInterface
{
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
