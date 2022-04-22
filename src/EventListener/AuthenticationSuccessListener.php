<?php

namespace CodeBuds\SyliusFCMPlugin\EventListener;

use App\Entity\User\ShopUser;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;
use CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMToken;
use CodeBuds\SyliusFCMPlugin\Entity\TopicSubscription;
use CodeBuds\SyliusFCMPlugin\Service\FCMTopicService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationSuccessListener
{
    private RequestStack $requestStack;
    private EntityManagerInterface $manager;
    private FCMTopicService $topicService;

    public function __construct
    (
        RequestStack           $requestStack,
        EntityManagerInterface $manager,
        FCMTopicService        $topicService
    )
    {
        $this->requestStack = $requestStack;
        $this->manager = $manager;
        $this->topicService = $topicService;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        /** @var ShopUser $user */
        $user = $event->getUser();

        if (!$user instanceof FCMTokenOwnerInterface) {
            return;
        }

        $fcmTokenValue = $this->requestStack->getCurrentRequest()->get('FCMToken');

        if (!$fcmTokenValue) {
            return;
        }

        if ($user->hasToken($fcmTokenValue)) {
            return;
        }

        $newFCMToken = (new ShopUserFCMToken())->setShopUser($user)->setValue($fcmTokenValue);

        $this->manager->persist($newFCMToken);
        $this->manager->flush();

        $this->topicService->newTokenSubscriptions($user, $newFCMToken);
    }
}
