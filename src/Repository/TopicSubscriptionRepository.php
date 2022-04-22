<?php

namespace CodeBuds\SyliusFCMPlugin\Repository;

use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTopicInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class TopicSubscriptionRepository extends EntityRepository implements TopicSubscriptionRepositoryInterface
{

    public function getSubscribedTopics(FCMTokenOwnerInterface $user)
    {
        return $this->createQueryBuilder('topic_subscriptions')
            ->andWhere('topic_subscriptions.token IN (:tokens)')
            ->setParameter('tokens', $user->getFcmTokens())
            ->groupBy('topic_subscriptions.topic')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getSubscribedTopic(FCMTokenOwnerInterface $user, FCMTopicInterface $topic)
    {
        return $this->createQueryBuilder('topic_subscriptions')
            ->andWhere('topic_subscriptions.token IN (:tokens)')
            ->andWhere('topic_subscriptions.topic IN (:topic)')
            ->setParameter('tokens', $user->getFcmTokens())
            ->setParameter('topic', $topic)
            ->groupBy('topic_subscriptions.topic')
            ->getQuery()
            ->getOneOrNullResult();
    }

}
