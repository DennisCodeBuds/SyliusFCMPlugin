<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;


use Sylius\Component\Product\Model\ProductInterface;

class ProductFCMTopic extends FCMEntityTopic implements ProductFCMTopicInterface
{
    private ProductInterface $product;

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function setProduct(ProductInterface $product): ProductFCMTopicInterface
    {
        $this->product = $product;
        return $this;
    }

    public function generateTopicId(): self
    {
        $this->topicId = sprintf('product_%d', $this->product->getId());
        return $this;
    }


}
