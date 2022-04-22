<?php

namespace CodeBuds\SyliusFCMPlugin\Repository;

use CodeBuds\SyliusFCMPlugin\Entity\FCMConfigurationInterface;

interface FCMConfigurationRepositoryInterface
{
    public function findFirstConfigurationId(): ?int;

    public function findFirstConfiguration(): ?FCMConfigurationInterface;
}
