<?php

namespace CodeBuds\SyliusFCMPlugin\Service;

use App\Entity\FCM\ProductSaleFCMTopic;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTopicInterface;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopic;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopicInterface;
use CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription;
use CodeBuds\SyliusFCMPlugin\Repository\EntityTopicRepositoryInterface;
use CodeBuds\SyliusFCMPlugin\Repository\TopicSubscriptionRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FCMTopicService
{
    private FCMTokenService $tokenService;
    private EntityManagerInterface $manager;
    private TopicSubscriptionRepositoryInterface $subscriptionRepository;
    private ValidatorInterface $validator;
    private Messaging $messaging;
    private EntityTopicRepositoryInterface $entityTopicRepository;

    public function __construct(
        FCMTokenService                      $tokenService,
        EntityManagerInterface               $manager,
        TopicSubscriptionRepositoryInterface $subscriptionRepository,
        EntityTopicRepositoryInterface       $entityTopicRepository,
        ValidatorInterface                   $validator,
        Messaging                            $messaging
    )
    {
        $this->tokenService = $tokenService;
        $this->manager = $manager;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->validator = $validator;
        $this->messaging = $messaging;
        $this->entityTopicRepository = $entityTopicRepository;
    }

    public function generateProductTopic(ProductInterface $product): ProductFCMTopicInterface
    {
        return (new ProductFCMTopic())
            ->setProduct($product)
            ->generateTopicId();
    }

    public function getProductTopic(ProductInterface $product): ?ProductFCMTopicInterface
    {
        return $this->entityTopicRepository->getProductFCMTopicByProduct($product);
    }

    public function getDiscriminatorValueFromClass(string $class): string
    {
        $typeClass = new \ReflectionClass($class);
        $typeShortName = $typeClass->getShortName();
        return strtolower($typeShortName);
    }

    public function unsubscribe(FCMTopicInterface $topic): void
    {
        $tokens = $this->tokenService->getCurrentUserTokens();
        if ($tokens->isEmpty()) {
            return;
        }

        $query = $this->manager->createQuery('DELETE FROM CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription ts WHERE ts.token IN (:tokens) AND ts.topic = :topic')
            ->setParameter('tokens', $tokens)
            ->setParameter('topic', $topic);

        $query->execute();

        $this->messaging->unsubscribeFromTopic($topic->getTopicId(), $this->tokenService->getTokenValues($tokens));
    }

    public function unsubscribeUserFromTopic(FCMTokenOwnerInterface $user, FCMTopicInterface $topic, bool $deleteFromDatabase = true): void
    {
        $tokens = $user->getFcmTokens();
        if ($tokens->isEmpty()) {
            return;
        }

        if ($deleteFromDatabase) {
            $query = $this->manager->createQuery('DELETE FROM CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription ts WHERE ts.token IN (:tokens) AND ts.topic = :topic')
                ->setParameter('tokens', $tokens)
                ->setParameter('topic', $topic);

            $query->execute();
        }

        $this->messaging->unsubscribeFromTopic($topic->getTopicId(), $this->tokenService->getTokenValues($tokens));
    }

    public function unsubscribeUserFromTopicId(FCMTokenOwnerInterface $user, string $topicId): void
    {
        $tokens = $user->getFcmTokens();
        if ($tokens->isEmpty()) {
            return;
        }

        $this->messaging->unsubscribeFromTopic($topicId, $this->tokenService->getTokenValues($tokens));
    }

    /**
     * @param FCMTokenOwnerInterface $user
     * @param string|null $type
     * @return void
     */
    public function unsubscribeUserFromTopics(FCMTokenOwnerInterface $user, ?string $type): void
    {
        $tokens = $user->getFcmTokens();

        if ($tokens->isEmpty()) {
            return;
        }

        $subscriptions = $this->subscriptionRepository->getSubscribedTopics($user, $type);

        if (!$subscriptions) {
            return;
        }

        $topicIds = [];
        /** @var TopicSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            $this->manager->remove($subscription);
            $topicIds[] = $subscription->getTopic()->getTopicId();
        }
        $this->manager->flush();

        $this->messaging->unsubscribeFromTopics($topicIds, $this->tokenService->getTokenValues($tokens));
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

        if ($topics) {
            try {
                $this->messaging->validateRegistrationTokens($newFCMToken);
                $this->messaging->subscribeToTopics($topics, $newFCMToken);
            } catch (MessagingException|FirebaseException $e) {

            }
        }

        $this->manager->flush();
    }

    public function isSubscribed(FCMTokenOwnerInterface $user, FCMTopicInterface $topic): bool
    {
        return (bool)$this->subscriptionRepository->getSubscribedTopic($user, $topic);
    }

    public function generateSubscriptions(FCMTopicInterface $topic): int
    {
        $tokens = $this->tokenService->getCurrentUserTokens();
        if ($tokens->isEmpty()) {
            return 0;
        }
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

    public function generateSubscriptionsForUser(FCMTokenOwnerInterface $user, FCMTopicInterface $topic): int
    {
        $tokens = $user->getFcmTokens();
        if ($tokens->isEmpty()) {
            return 0;
        }
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
        if ($tokens->isEmpty()) {
            return [];
        }
        return $this->messaging->unsubscribeFromAllTopics($this->tokenService->getTokenValues($tokens));
    }

    public function unsubscribeUserFromAllTopics(FCMTokenOwnerInterface $user): array
    {
        /** @var FCMTokenInterface[]|Collection $tokens */
        $tokens = $user->getFcmTokens();
        if ($tokens->isEmpty()) {
            return [];
        }
        return $this->messaging->unsubscribeFromAllTopics($this->tokenService->getTokenValues($tokens));
    }

    /**
     * @param FCMTopicInterface $topic
     * @param FCMTokenInterface[] $tokens $tokens
     * @return array
     */
    public function unsubscribeTokensFromTopic(FCMTopicInterface $topic, array $tokens): array
    {
        return $this->messaging->unsubscribeFromTopic($topic->getTopicId(), $this->tokenService->getTokenValues($tokens));
    }

    /**
     * @param TopicSubscription[] $subscriptions
     * @return void
     */
    public function deleteSubscriptionsFromDatabase(array $subscriptions): void
    {
        foreach ($subscriptions as $subscription) {
            $this->manager->remove($subscription);
        }
        $this->manager->flush();
    }

    public function deleteTopic(FCMTopicInterface $topic): void
    {
        /** @var TopicSubscription[] $subscriptions */
        $subscriptions = $topic->getSubscriptions()->toArray();
        if (count($subscriptions)) {
            $subscribedTokens = array_map(static fn($subscription) => dd($subscription), $subscriptions);
            if (count($subscribedTokens) > 2000) {
                $subscribedTokensChunks = array_chunk($subscribedTokens, 2000);
                foreach ($subscribedTokensChunks as $chunk) {
                    $this->unsubscribeTokensFromTopic($topic, $chunk);
                }
            } else {
                $this->unsubscribeTokensFromTopic($topic, $subscribedTokens);
            }
            $this->deleteSubscriptionsFromDatabase($subscriptions);
        }

        //Delete all notifications related to the topic
        $query = $this->manager->createQuery('DELETE FROM CodeBuds\SyliusFCMPlugin\Entity\ProductFCMNotification notification WHERE notification.topic = :topic')
            ->setParameter('topic', $topic);

        $query->execute();

        dd('kek');

        $this->manager->remove($topic);
        $this->manager->flush();
    }
}
