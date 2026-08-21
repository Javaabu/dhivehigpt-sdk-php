<?php

namespace Javaabu\DhivehiGpt\Tests\Unit\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Javaabu\DhivehiGpt\Http\Client;
use Javaabu\DhivehiGpt\Tests\TestSupport\InteractsWithLocalServer;
use PHPUnit\Framework\TestCase;

/**
 * Exercises Client against a real HTTP server (PHP's built-in development
 * server) so the JSON body encode/decode and timeout wiring around a real
 * Guzzle client is verified end-to-end, in addition to the fast
 * MockHandler-based tests in ClientTest.
 */
class ClientIntegrationTest extends TestCase
{
    use InteractsWithLocalServer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->startLocalServer();
    }

    protected function tearDown(): void
    {
        $this->stopLocalServer();

        parent::tearDown();
    }

    protected function makeClient(array $guzzle_options = []): Client
    {
        return new Client(new GuzzleClient(array_merge([
            'base_uri' => "http://127.0.0.1:{$this->port}/v1/",
        ], $guzzle_options)));
    }

    public function test_it_sends_a_get_request_with_the_correct_url(): void
    {
        $result = $this->makeClient()->get('voices', ['gender' => 'female']);

        $this->assertSame('GET', $result['method']);
        $this->assertSame('/v1/voices', $result['path']);
    }

    public function test_it_sends_a_post_request_with_a_json_encoded_body(): void
    {
        $result = $this->makeClient()->post('tts', ['text' => 'hello', 'voice' => 'hajja']);

        $this->assertSame('POST', $result['method']);
        $this->assertSame('/v1/tts', $result['path']);
        $this->assertSame(json_encode(['text' => 'hello', 'voice' => 'hajja']), $result['body']);
    }

    public function test_it_lets_guzzle_throw_for_an_error_status(): void
    {
        $this->expectException(ClientException::class);

        $this->makeClient()->get('voices/unknown-voice', ['status' => 404]);
    }

    public function test_a_per_request_timeout_overrides_the_default_timeout(): void
    {
        $this->expectException(GuzzleException::class);

        $this->makeClient()->get('voices', ['sleep' => 1], 0.2);
    }

    public function test_a_per_request_timeout_can_allow_more_time_than_the_default(): void
    {
        $client = $this->makeClient(['timeout' => 0.2]);

        $result = $client->get('voices', ['sleep' => 1], 5.0);

        $this->assertSame('GET', $result['method']);
    }
}
