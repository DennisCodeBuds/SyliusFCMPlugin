<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

class ShopUserFCMNotification extends FCMNotification
{
    protected $shopUser;

    public function getShopUser(): ?FCMRecipientInterface
    {
        return $this->shopUser;
    }

    public function setShopUser(FCMRecipientInterface $shopUser): self
    {
        $this->shopUser = $shopUser;
        return $this;
    }
}
