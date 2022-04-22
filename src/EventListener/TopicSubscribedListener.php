<?php

namespace CodeBuds\SyliusFCMPlugin\EventListener;

use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTopicInterface;
use CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription;
use CodeBuds\SyliusFCMPlugin\Event\TopicSubscribedEvent;
use CodeBuds\SyliusFCMPlugin\Service\FCMTokenService;
use CodeBuds\SyliusFCMPlugin\Service\FCMTopicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TopicSubscribedListener
{
    private FCMTokenService $tokenService;
    private FCMTopicService $topicService;

    public function __construct(
        FCMTokenService        $tokenService,
        FCMTopicService        $topicService
    )
    {
        $this->tokenService = $tokenService;
        $this->topicService = $topicService;
    }

    public function onFcmTopicSubscribed(TopicSubscribedEvent $event): void
    {
        $topicSubscription = $event->getData();
        $tokens = $this->tokenService->getCurrentUserTokens();

        $tokenSubscriptionCount = $this->topicService->generateSubscriptions($topicSubscription);

        $event->setTopicSubscription($topicSubscription);
        $event->setSubscriptionCount($tokenSubscriptionCount);
        $event->setTokens($tokens);
    }
}
