<?php

namespace CodeBuds\SyliusFCMPlugin\Service;

use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTopicInterface;
use CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription;
use CodeBuds\SyliusFCMPlugin\Repository\TopicSubscriptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Messaging;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FCMTopicService
{
    private FCMTokenService $tokenService;
    private EntityManagerInterface $manager;
    private TopicSubscriptionRepositoryInterface $subscriptionRepository;
    private ValidatorInterface $validator;
    private Messaging $messaging;

    public function __construct(
        FCMTokenService                      $tokenService,
        EntityManagerInterface               $manager,
        TopicSubscriptionRepositoryInterface $subscriptionRepository,
        ValidatorInterface                   $validator,
        Messaging                            $messaging
    )
    {
        $this->tokenService = $tokenService;
        $this->manager = $manager;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->validator = $validator;
        $this->messaging = $messaging;
    }

    public function unsubscribe(FCMTopicInterface $topic): void
    {
        $tokens = $this->tokenService->getCurrentUserTokens();

        $query = $this->manager->createQuery('DELETE FROM CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription ts WHERE ts.token IN (:tokens) AND ts.topic = :topic')
            ->setParameter('tokens', $tokens)
            ->setParameter('topic', $topic);

        $query->execute();

        $this->messaging->unsubscribeFromTopic($topic->getTopicId(), $this->tokenService->getTokenValues($tokens));
    }

    public function unsubscribeUserFromTopic(FCMTokenOwnerInterface $user, FCMTopicInterface $topic): void
    {
        $tokens = $user->getCurrentUserTokens();

        $query = $this->manager->createQuery('DELETE FROM CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription ts WHERE ts.token IN (:tokens) AND ts.topic = :topic')
            ->setParameter('tokens', $tokens)
            ->setParameter('topic', $topic);

        $query->execute();

        $this->messaging->unsubscribeFromTopic($topic->getTopicId(), $this->tokenService->getTokenValues($tokens));
    }

    public function newTokenSubscriptions(FCMTokenOwnerInterface $user, FCMTokenInterface $newFCMToken): void
    {
        $subscriptions = $this->subscriptionRepository->getSubscribedTopics($user);
        $topics = [];

        /** @var TopicSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            $newSubscription = (new TopicSubscription())
                ->setTopic($subscription->getTopic())
                ->setToken($newFCMToken);
            $this->manager->persist($newSubscription);
            $topics[] = $subscription->getTopic()->getTopicId();
        }

        $this->messaging->subscribeToTopics($topics, $newFCMToken);

        $this->manager->flush();


    }

    public function isSubscribed(FCMTokenOwnerInterface $user, FCMTopicInterface $topic): bool
    {
        return (bool)$this->subscriptionRepository->getSubscribedTopic($user, $topic);
    }

    public function generateSubscriptions(FCMTopicInterface $topic): int
    {
        $tokens = $this->tokenService->getCurrentUserTokens();
        $count = 0;
        foreach ($tokens as $token) {
            $tokenSubscription = new TopicSubscription();
            $tokenSubscription->setTopic($topic);
            $tokenSubscription->setToken($token);

            $errors = $this->validator->validate($tokenSubscription, null, 'codebuds');

            // Only persist the subscription for this topic/token  if there are no errors
            if (count($errors) === 0) {
                $count++;
                $this->manager->persist($tokenSubscription);
            }

        }

        $this->messaging->SubscribeToTopic($topic->getTopicId(), $this->tokenService->getTokenValues($tokens));

        return $count;
    }

    public function unsubscribeCurrentUserFromAllTopics(): array
    {
        $tokens = $this->tokenService->getCurrentUserTokens();
        return $this->messaging->unsubscribeFromAllTopics($this->tokenService->getTokenValues($tokens));
    }

    public function unsubscribeUserFromAllTopics(FCMTokenOwnerInterface $user): array
    {
        /** @var FCMTokenInterface[] $tokens */
        $tokens = $user->getFcmTokens();
        return $this->messaging->unsubscribeFromAllTopics($this->tokenService->getTokenValues($tokens));
    }
}
