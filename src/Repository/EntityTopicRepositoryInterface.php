<?php

namespace CodeBuds\SyliusFCMPlugin\Repository;

use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopic;
use Sylius\Component\Product\Model\ProductInterface;

interface EntityTopicRepositoryInterface
{
    public function getProductsWithoutTopic();

    public function getProductFCMTopicByProduct(ProductInterface $product): ?ProductFCMTopic;
}
