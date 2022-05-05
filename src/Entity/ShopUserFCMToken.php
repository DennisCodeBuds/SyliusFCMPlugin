<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Sylius\Component\Core\Model\ShopUserInterface;

class ShopUserFCMToken extends FCMToken
{
    protected $shopUser;

    /**
     * @return mixed
     */
    public function getShopUser(): ShopUserInterface
    {
        return $this->shopUser;
    }

    /**
     * @param mixed $shopUser
     * @return ShopUserFCMToken
     */
    public function setShopUser(ShopUserInterface $shopUser): ShopUserFCMToken
    {
        $this->shopUser = $shopUser;
        return $this;
    }


}
