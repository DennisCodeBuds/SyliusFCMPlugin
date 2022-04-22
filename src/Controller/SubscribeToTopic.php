<?php

namespace CodeBuds\SyliusFCMPlugin\Controller;

use CodeBuds\SyliusFCMPlugin\Event\TopicSubscribedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class SubscribeToTopic
{
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $manager;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $manager)
    {
        $this->dispatcher = $dispatcher;
        $this->manager = $manager;
    }

    public function __invoke($data): Response
    {
        $event = new TopicSubscribedEvent($data);

        $this->dispatcher->dispatch($event, TopicSubscribedEvent::NAME);

        $this->manager->flush();

        return new Response('', 204);
    }
}
