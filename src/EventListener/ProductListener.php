<?php

namespace CodeBuds\SyliusFCMPlugin\EventListener;

use CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription;
use CodeBuds\SyliusFCMPlugin\Service\FCMTopicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ProductListener
{
    private FCMTopicService $topicService;
    private EntityManagerInterface $manager;

    public function __construct(FCMTopicService $topicService, EntityManagerInterface $manager)
    {
        $this->topicService = $topicService;
        $this->manager = $manager;
    }

    public function postCreate(GenericEvent $event): void
    {
        $product = $event->getSubject();
        $topic = $this->topicService->generateProductTopic($product);
        $this->manager->persist($topic);
        $this->manager->flush();
    }

    public function postDelete(GenericEvent $event): void
    {
        $product = $event->getSubject();
        $topic = $this->topicService->getProductTopic($product);

        if(!$topic) {
            return;
        }

        $this->topicService->deleteTopic($topic);
    }
}
