<?php


namespace CodeBuds\SyliusFCMPlugin\Service;


use CodeBuds\SyliusFCMPlugin\Entity\FCMEntityTopicInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMNotificationInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMRecipientInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMNotification;
use CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMNotification;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class FirebaseMessaging
{
    private Messaging $messaging;
    private EntityManagerInterface $manager;
    private LoggerInterface $logger;

    public function __construct(
        Messaging              $messaging,
        EntityManagerInterface $manager,
        LoggerInterface        $logger
    )
    {
        $this->messaging = $messaging;
        $this->manager = $manager;
        $this->logger = $logger;
    }

    /**
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function sendPublicMessage(string $topic, string $title, string $body, ?array $data = null, bool $validateOnly = false): array
    {
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create($title, $body));

        if ($data) {
            $message = $message->withData($data);
        }

        return $this->messaging->send($message, $validateOnly);
    }

    /**
     * @param FCMRecipientInterface $recipient
     * @param string $title
     * @param string $body
     * @param array $data
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function sendPrivateMessage(FCMRecipientInterface $recipient, string $title, string $body, array $data = []): void
    {
        $message = (new ShopUserFCMNotification())
            ->setRecipient($recipient)
            ->setTitle($title)
            ->setBody($body);

        $this->manager->persist($message);
        $this->manager->flush();

        array_map(function ($token) use ($title, $body, $data) {
            $message = CloudMessage::withTarget('token', (string)$token)
                ->withNotification(Notification::create($title, $body));

            if ($data) {
                $message = $message->withData($data);
            }
            $this->messaging->send($message);
        }, $recipient->getFcmTokens()->toArray());
    }

    public function sendShopUserNotification(ShopUserFCMNotification $notification): void
    {
        $this->manager->persist($notification);
        $this->manager->flush();

        array_map(/**
         * @throws MessagingException
         * @throws FirebaseException
         */ function ($token) use ($notification) {
            $message = CloudMessage::withTarget('token', (string)$token)
                ->withNotification(Notification::create($notification->getTitle(), $notification->getBody()));

            if ($data = $notification->getData()) {
                $message = $message->withData($data);
            }
            $this->messaging->send($message);
        }, $notification->getShopUser()->getFcmTokens()->toArray());
    }

    /**
     * @throws MessagingException
     * @throws FirebaseException
     * @throws \JsonException
     */
    public function sendProductNotification(ProductFCMNotification $notification): void
    {
        $this->manager->persist($notification);
        $this->manager->flush();
        $message = CloudMessage::withTarget('topic', $notification->getTopic()->getTopicId())
            ->withNotification(Notification::create($notification->getTitle(), $notification->getBody()));

        if ($data = $notification->getData()) {
            $message = $message->withData(json_decode($data, true, 512, JSON_THROW_ON_ERROR));
        }
        $this->messaging->send($message);
    }

    /**
     * @throws MessagingException
     * @throws FirebaseException
     * @throws \JsonException
     */
    public function sendEntityTopicNotification(FCMNotificationInterface $notification): void
    {
        $this->manager->persist($notification);
        $this->manager->flush();
        $message = CloudMessage::withTarget('topic', $notification->getTopic()->getTopicId())
            ->withNotification(Notification::create($notification->getTitle(), $notification->getBody()));

        if ($data = $notification->getData()) {
            $message = $message->withData(json_decode($data, true, 512, JSON_THROW_ON_ERROR));
        }
        $this->messaging->send($message);
    }

    /**
     * @throws MessagingException
     * @throws FirebaseException
     */
    public function sendTestMessage(string $target, string $title, string $body, array $data = [], ?string $imageUrl = null, string $targetType = 'token'): void
    {
        $message = CloudMessage::withTarget($targetType, $target)
            ->withNotification(Notification::create($title, $body));

        if ($data) {
            $message = $message->withData($data);
        }
        $this->messaging->send($message);
    }

    public function getTopicSubscriptions(FCMTokenOwnerInterface $user): array
    {
        $tokens = $user->getFcmTokens();

        $subscriptions = [];
        /**
         * @var FCMTokenInterface $token
         */
        foreach ($tokens as $token) {
            try {
                $appInstance = $this->messaging->getAppInstance($token->getValue());
                foreach ($appInstance->topicSubscriptions() as $subscription) {
                    $subscriptions[] = $subscription;
                };
            } catch (FirebaseException $e) {
                $this->logger->log(LogLevel::DEBUG, $e->getMessage());
            }
        }


        return $subscriptions;
    }
}
