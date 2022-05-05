<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

interface TopicSubject
{
    public function getTopic(): ?FCMTopicInterface;
}
