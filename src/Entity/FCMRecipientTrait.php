<?php

namespace CodeBuds\SyliusFCMPlugin\Entity;

use Doctrine\Common\Collections\Collection;

trait FCMRecipientTrait
{
    protected $messages;

    /**
     * @return null|Collection|FCMNotification[]
     */
    public function getNotifications(): ?Collection
    {
        return $this->messages;
    }

    public function addNotification(FCMNotificationInterface $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setRecipient($this);
        }

        return $this;
    }

    public function removeNotification(FCMNotificationInterface $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getRecipient() === $this) {
                $message->setRecipient(null);
            }
        }

        return $this;
    }
}
