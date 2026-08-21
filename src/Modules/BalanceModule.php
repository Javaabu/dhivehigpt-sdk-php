<?php

namespace Javaabu\DhivehiGpt\Modules;

/**
 * View the subscription and credit balance associated with the current API key team.
 */
class BalanceModule extends Module
{
    /**
     * Get the current API key team's balance, subscription, and active billing period.
     *
     * @return array<string, mixed>
     */
    public function get(?float $timeout = null): array
    {
        return $this->client->get('balance', [], $timeout);
    }
}
