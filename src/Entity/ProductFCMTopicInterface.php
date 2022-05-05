<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

interface ProductFCMTopicInterface extends FCMEntityTopicInterface
{
    public function generateTopicId(): self;
}
