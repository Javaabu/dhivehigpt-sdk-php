<?php

namespace Javaabu\DhivehiGpt\Tests\Unit\Modules;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Javaabu\DhivehiGpt\Modules\VoicesModule;
use Javaabu\DhivehiGpt\Tests\TestSupport\MocksGuzzle;
use PHPUnit\Framework\TestCase;

class VoicesModuleTest extends TestCase
{
    use MocksGuzzle;

    public function test_list_requests_the_voices_endpoint(): void
    {
        $module = new VoicesModule($this->makeClient([new Response(200, [], '{"data":[{"slug":"hajja"}]}')]));

        $result = $module->list();

        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('https://api.dhivehigpt.com/v1/voices', (string) $this->lastRequest()->getUri());
        $this->assertSame(['data' => [['slug' => 'hajja']]], $result);
    }

    public function test_list_forwards_filters_as_query_parameters(): void
    {
        $module = new VoicesModule($this->makeClient([new Response(200, [], '{"data":[]}')]));

        $module->list([
            'search' => 'haj',
            'per_page' => 5,
            'page' => 2,
            'sort' => '-name',
            'is_premium' => false,
            'gender' => 'female',
            'age_group' => 'thirties',
        ]);

        $this->assertSame(
            'https://api.dhivehigpt.com/v1/voices?search=haj&per_page=5&page=2&sort=-name&is_premium=0&gender=female&age_group=thirties',
            (string) $this->lastRequest()->getUri()
        );
    }

    public function test_list_forwards_a_custom_timeout(): void
    {
        $module = new VoicesModule($this->makeClient([new Response(200, [], '{"data":[]}')]));

        $module->list([], 5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_get_requests_a_single_voice_by_slug(): void
    {
        $module = new VoicesModule($this->makeClient([new Response(200, [], '{"data":{"slug":"hajja","gender":"female"}}')]));

        $result = $module->get('hajja');

        $this->assertSame('https://api.dhivehigpt.com/v1/voices/hajja', (string) $this->lastRequest()->getUri());
        $this->assertSame(['data' => ['slug' => 'hajja', 'gender' => 'female']], $result);
    }

    public function test_get_forwards_a_custom_timeout(): void
    {
        $module = new VoicesModule($this->makeClient([new Response(200, [], '{"data":{}}')]));

        $module->get('hajja', 5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_get_lets_guzzle_throw_for_an_unknown_slug(): void
    {
        $module = new VoicesModule($this->makeClient([new Response(404, [], '{"message":"Not found"}')]));

        $this->expectException(ClientException::class);

        $module->get('unknown-voice');
    }
}
