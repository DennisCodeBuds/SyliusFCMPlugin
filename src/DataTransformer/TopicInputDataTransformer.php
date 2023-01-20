<?php

namespace CodeBuds\SyliusFCMPlugin\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription;
use CodeBuds\SyliusFCMPlugin\Event\TopicSubscribedEvent;
use CodeBuds\SyliusFCMPlugin\Repository\EntityTopicRepository;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TopicInputDataTransformer implements DataTransformerInterface
{
    private ProductRepositoryInterface $productRepository;
    private EventDispatcherInterface $dispatcher;
    private EntityTopicRepository $topicRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        EntityTopicRepository      $topicRepository,
        EventDispatcherInterface   $dispatcher
    )
    {
        $this->productRepository = $productRepository;
        $this->dispatcher = $dispatcher;
        $this->topicRepository = $topicRepository;
    }

    public function transform($object, string $to, array $context = [])
    {
        $type = $object->type;
        if ($type === 'product') {
            $product = $this->productRepository->findOneBy(['code' => $object->identifier]);
            if(!$product) {
                throw new NotFoundHttpException('No such product');
            }
            $topic = $this->topicRepository->getProductFCMTopicByProduct($product);
            if(!$topic) {
                throw new NotFoundHttpException('No topic found for the product');
            }

            return (new TopicSubscription())
                ->setTopic($topic);
        }
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof TopicSubscription) {
            return false;
        }

        return TopicSubscription::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
