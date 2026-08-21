<?php

namespace Javaabu\DhivehiGpt\Tests\Unit\Modules;

use GuzzleHttp\Psr7\Response;
use Javaabu\DhivehiGpt\Modules\BalanceModule;
use Javaabu\DhivehiGpt\Tests\TestSupport\MocksGuzzle;
use PHPUnit\Framework\TestCase;

class BalanceModuleTest extends TestCase
{
    use MocksGuzzle;

    public function test_get_requests_the_balance_endpoint(): void
    {
        $module = new BalanceModule($this->makeClient([
            new Response(200, [], '{"team":{"uuid":"abc"},"subscription":null,"billing_period":null}'),
        ]));

        $result = $module->get();

        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('https://api.dhivehigpt.com/v1/balance', (string) $this->lastRequest()->getUri());
        $this->assertSame(['team' => ['uuid' => 'abc'], 'subscription' => null, 'billing_period' => null], $result);
    }

    public function test_get_forwards_a_custom_timeout(): void
    {
        $module = new BalanceModule($this->makeClient([new Response(200, [], '{"team":{}}')]));

        $module->get(5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }
}
