<?php

namespace CodeBuds\SyliusFCMPlugin\Repository;

use CodeBuds\SyliusFCMPlugin\Entity\FCMConfigurationInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class FCMConfigurationRepository extends EntityRepository implements FCMConfigurationRepositoryInterface
{
    public function findFirstConfigurationId(): ?int
    {
        return $this->createQueryBuilder('configuration')
            ->select('configuration.id')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findFirstConfiguration(): ?FCMConfigurationInterface
    {
        return $this->createQueryBuilder('configuration')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
