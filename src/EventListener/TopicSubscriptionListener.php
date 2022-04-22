<?php

namespace CodeBuds\SyliusFCMPlugin\EventListener;

use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use CodeBuds\SyliusFCMPlugin\Entity\TopicSubscriptionInterface;
use CodeBuds\SyliusFCMPlugin\Event\TopicSubscribedEvent;
use CodeBuds\SyliusFCMPlugin\Service\FCMTokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TopicSubscriptionListener
{
    private FCMTokenService $tokenService;
    private EntityManagerInterface $manager;
    private ValidatorInterface $validator;

    public function __construct(
        FCMTokenService        $tokenService,
        EntityManagerInterface $manager,
        ValidatorInterface     $validator
    )
    {
        $this->tokenService = $tokenService;
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function onFcmTopicSubscribed(TopicSubscribedEvent $event): void
    {
        $topicSubscription = $event->getData();
        $tokens = $this->tokenService->getCurrentUserTokens();

        $tokenSubscriptionCount = $this->generateSubscriptions($topicSubscription, $tokens);

        $event->setTopicSubscription($topicSubscription);
        $event->setSubscriptionCount($tokenSubscriptionCount);
        $event->setTokens($tokens);
    }

    /**
     * @param TopicSubscriptionInterface $topicSubscription
     * @param FCMTokenInterface[] $tokens
     * @return int
     */
    private function generateSubscriptions(TopicSubscriptionInterface $topicSubscription, $tokens): int
    {
        $count = 0;
        foreach ($tokens as $token) {
            $tokenSubscription = clone $topicSubscription;
            $tokenSubscription->setToken($token);

            $errors = $this->validator->validate($tokenSubscription, null, 'codebuds');

            // Only persist the subscription for this topic/token  if there are no errors
            if (count($errors) === 0) {
                $count++;
                $this->manager->persist($tokenSubscription);
            }

        }

        return $count;
    }
}
