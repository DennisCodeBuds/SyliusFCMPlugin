<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Doctrine\Common\Collections\Collection;

trait FCMTokenOwnerTrait
{
    private $fcmTokens;

    /**
     * @return null|Collection|FCMToken[]
     */
    public function getFcmTokens(): ?Collection
    {
        return $this->fcmTokens;
    }

    public function addFcmToken(FCMToken $fcmToken): self
    {
        if (!$this->fcmTokens->contains($fcmToken)) {
            $this->fcmTokens[] = $fcmToken;
            $fcmToken->setOwner($this);
        }

        return $this;
    }

    public function removeFcmToken(FCMToken $fcmToken): self
    {
        if ($this->fcmTokens->removeElement($fcmToken)) {
            // set the owning side to null (unless already changed)
            if ($fcmToken->getOwner() === $this) {
                $fcmToken->setOwner(null);
            }
        }

        return $this;
    }

    public function hasToken(string $fcmTokenValue): bool
    {
        if(!$this->getFcmTokens()) {
            return false;
        }

        foreach ($this->getFcmTokens() as $token) {
            if($token->getValue() === $fcmTokenValue) {
                return true;
            }
        }
        return false;
    }
}
