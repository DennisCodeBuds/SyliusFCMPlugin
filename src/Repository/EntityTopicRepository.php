<?php

namespace CodeBuds\SyliusFCMPlugin\Repository;

use App\Entity\Product\Product;
use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopic;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopic;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductInterface;

class EntityTopicRepository implements EntityTopicRepositoryInterface
{
    public EntityRepository $repository;
    private EntityRepository $productTopicRepository;
    private EntityRepository $productRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(FCMEntityTopic::class);
        $this->productTopicRepository = $entityManager->getRepository(ProductFCMTopic::class);
        $this->productRepository = $entityManager->getRepository(Product::class);
    }

    public function getProductsWithoutTopic()
    {
        $productTopics = $this->productTopicRepository->createQueryBuilder('product_topic')
            ->innerJoin('product_topic.product', 'product')
            ->select('product.id')
            ->getQuery()
            ->getScalarResult();

        $productsWithoutTopics = $this->productRepository->createQueryBuilder('product');

        if ($productTopics) {
            $productsWithoutTopics
                ->andWhere('product.id NOT IN (:products)')
                ->setParameter(':products', $productTopics);
        }

        return $productsWithoutTopics
            ->getQuery()
            ->getResult();
    }

    public function getProductFCMTopicByProduct(ProductInterface $product): ?ProductFCMTopic {
        return $this->productTopicRepository->createQueryBuilder('product_topic')
            ->andWhere('product_topic.product = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
