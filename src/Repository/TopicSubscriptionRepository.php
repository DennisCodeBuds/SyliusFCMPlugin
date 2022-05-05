<?php

namespace CodeBuds\SyliusFCMPlugin\Repository;

use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTopicInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class TopicSubscriptionRepository extends EntityRepository implements TopicSubscriptionRepositoryInterface
{
    /**
     * @throws \ReflectionException
     */
    public function getSubscribedTopics(FCMTokenOwnerInterface $user, ?string $type = null)
    {
        $qb = $this->createQueryBuilder('topic_subscriptions')
            ->andWhere('topic_subscriptions.token IN (:tokens)')
            ->setParameter('tokens', $user->getFcmTokens())
            ->groupBy('topic_subscriptions.topic');

        if ($type) {
            $typeClass = new \ReflectionClass($type);
            $typeShortName = $typeClass->getShortName();
            $qb->innerJoin('topic_subscriptions.topic', 'topic')
                ->andWhere('topic INSTANCE OF :type')
                ->setParameter('type', strtolower($typeShortName));
        }

        return $qb->getQuery()
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
