<?php

namespace CodeBuds\SyliusFCMPlugin\Dto;

class TopicOutput
{
    public bool $subscribed;

    public function isSubscribed(): bool
    {
        return $this->subscribed;
    }

    public function setSubscribed(bool $subscribed): TopicOutput
    {
        $this->subscribed = $subscribed;
        return $this;
    }
}
