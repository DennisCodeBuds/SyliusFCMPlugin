<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

interface FCMTokenInterface
{
    public function getId(): ?int;

    public function getOwner(): ?FCMTokenOwnerInterface;

    public function setOwner(?FCMTokenOwnerInterface $owner): FCMTokenInterface;

    public function getValue(): string;

    public function setValue(string $value): FCMTokenInterface;
}
