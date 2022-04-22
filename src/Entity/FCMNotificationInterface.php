<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

interface FCMNotificationInterface
{
    public function getId(): ?int;

    public function getTitle(): ?string;

    public function setTitle(string $title): FCMNotification;

    public function getBody(): ?string;

    public function setBody(?string $body): FCMNotification;

    public function getRecipient(): ?FCMRecipientInterface;

    public function setRecipient(?FCMRecipientInterface $recipient): FCMNotification;
}
