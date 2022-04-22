<?php

namespace CodeBuds\SyliusFCMPlugin\EventListener;

use CodeBuds\SyliusFCMPlugin\Event\TopicSubscribedEvent;
use CodeBuds\SyliusFCMPlugin\Service\FCMTopicService;

class TopicUnsubscribedListener
{
    private FCMTopicService $topicService;

    public function __construct(
        FCMTopicService        $topicService
    )
    {
        $this->topicService = $topicService;
    }

    public function onFcmTopicUnSubscribed(TopicSubscribedEvent $event): void
    {
        $topic = $event->getData();
        $this->topicService->unsubscribe($topic);
    }
}
