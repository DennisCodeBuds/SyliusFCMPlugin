<?php

namespace CodeBuds\SyliusFCMPlugin\EventListener;

use CodeBuds\SyliusFCMPlugin\Event\TopicSubscriptionToggleEvent;
use CodeBuds\SyliusFCMPlugin\Service\FCMTopicService;
use Symfony\Component\Security\Core\Security;

class ToggleTopicSubscriptionListener
{
    private FCMTopicService $topicService;
    private Security $security;

    public function __construct(
        FCMTopicService $topicService,
        Security        $security
    )
    {
        $this->topicService = $topicService;
        $this->security = $security;
    }

    public function onFcmTopicSubscriptionToggle(TopicSubscriptionToggleEvent $event): void
    {
        $topic = $event->getData();
        if ($this->topicService->isSubscribed($this->security->getUser(), $topic)) {
            $this->topicService->unsubscribe($topic);
            $event->setSubscribed(false);
        } else {
            $this->topicService->generateSubscriptions($topic);
            $event->setSubscribed(true);
        }
    }
}
