<?php

namespace CodeBuds\SyliusFCMPlugin\Controller;

use CodeBuds\SyliusFCMPlugin\Entity\FCMTopicInterface;
use CodeBuds\SyliusFCMPlugin\Event\TopicSubscriptionToggleEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ToggleTopicSubscription
{
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $manager;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $manager)
    {
        $this->dispatcher = $dispatcher;
        $this->manager = $manager;
    }

    /**
     * @param FCMTopicInterface $data
     * @return FCMTopicInterface
     */
    public function __invoke(FCMTopicInterface $data): FCMTopicInterface

    {
        $event = new TopicSubscriptionToggleEvent($data);

        $this->dispatcher->dispatch($event, TopicSubscriptionToggleEvent::NAME);

        $this->manager->flush();

        $data->setSubscribed($event->isSubscribed());

        return $data;
    }
}
