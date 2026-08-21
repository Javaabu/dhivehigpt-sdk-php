<?php

namespace Javaabu\DhivehiGpt\Tests\Unit;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Javaabu\DhivehiGpt\DhivehiGpt;
use Javaabu\DhivehiGpt\Modules\BalanceModule;
use Javaabu\DhivehiGpt\Modules\TasksModule;
use Javaabu\DhivehiGpt\Modules\TtsModule;
use Javaabu\DhivehiGpt\Modules\VoicesModule;
use Javaabu\DhivehiGpt\Tests\TestSupport\MocksGuzzle;
use PHPUnit\Framework\TestCase;

class DhivehiGptTest extends TestCase
{
    use MocksGuzzle;

    public function test_it_throws_when_the_api_key_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DhivehiGpt('');
    }

    public function test_it_throws_when_the_api_key_is_only_whitespace(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DhivehiGpt('   ');
    }

    public function test_it_exposes_the_default_base_url_and_api_version(): void
    {
        $this->assertSame('https://api.dhivehigpt.com', DhivehiGpt::DEFAULT_BASE_URL);
        $this->assertSame('v1', DhivehiGpt::DEFAULT_API_VERSION);
        $this->assertSame(30.0, DhivehiGpt::DEFAULT_TIMEOUT);
    }

    public function test_it_exposes_the_modules(): void
    {
        $client = new DhivehiGpt('test-api-key');

        $this->assertInstanceOf(VoicesModule::class, $client->voices());
        $this->assertInstanceOf(TtsModule::class, $client->tts());
        $this->assertInstanceOf(BalanceModule::class, $client->balance());
        $this->assertInstanceOf(TasksModule::class, $client->tasks());
    }

    public function test_each_module_is_a_singleton_on_the_client(): void
    {
        $client = new DhivehiGpt('test-api-key');

        $this->assertSame($client->voices(), $client->voices());
        $this->assertSame($client->tts(), $client->tts());
        $this->assertSame($client->balance(), $client->balance());
        $this->assertSame($client->tasks(), $client->tasks());
    }

    /**
     * @return array<string, mixed>
     */
    protected function mockHandlerOptions(): array
    {
        $mock_handler = new MockHandler([new Response(200, [], '{"data":{}}')]);
        $stack = HandlerStack::create($mock_handler);
        $stack->push(Middleware::history($this->guzzle_history));

        return ['handler' => $stack];
    }

    public function test_it_uses_the_configured_base_url_and_api_version_when_sending_requests(): void
    {
        $this->guzzle_history = [];

        $client = new DhivehiGpt('test-api-key', 'https://custom.example.com', 'v2', $this->mockHandlerOptions());

        $client->balance()->get();

        $this->assertSame('https://custom.example.com/v2/balance', (string) $this->lastRequest()->getUri());
    }

    public function test_it_sends_the_api_key_as_a_header(): void
    {
        $this->guzzle_history = [];

        $client = new DhivehiGpt('super-secret-key', DhivehiGpt::DEFAULT_BASE_URL, DhivehiGpt::DEFAULT_API_VERSION, $this->mockHandlerOptions());

        $client->balance()->get();

        $this->assertSame('super-secret-key', $this->lastRequest()->getHeaderLine('X-Api-Key'));
    }

    public function test_extra_guzzle_options_are_forwarded_to_the_client(): void
    {
        $this->guzzle_history = [];

        $options = $this->mockHandlerOptions();
        $options['headers'] = ['X-Custom-Header' => 'custom-value'];

        $client = new DhivehiGpt('test-api-key', DhivehiGpt::DEFAULT_BASE_URL, DhivehiGpt::DEFAULT_API_VERSION, $options);

        $client->balance()->get();

        $this->assertSame('custom-value', $this->lastRequest()->getHeaderLine('X-Custom-Header'));
    }
}
