<?php

namespace Javaabu\DhivehiGpt\Testing;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Javaabu\DhivehiGpt\DhivehiGpt;
use Javaabu\DhivehiGpt\Http\Client;
use PHPUnit\Framework\Assert as PHPUnit;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A fake DhivehiGpt client for testing: records every request made through
 * it and returns queued canned responses instead of making real network
 * calls. Construct directly for plain PHP tests, or use
 * Javaabu\DhivehiGpt\Facades\DhivehiGpt::fake() in a Laravel test.
 */
class DhivehiGptFake extends DhivehiGpt
{
    /**
     * @var array<int, ResponseInterface>
     */
    protected array $response_queue = [];

    /**
     * @var array<int, array{request: RequestInterface, options: array<string, mixed>}>
     */
    protected array $history = [];

    /**
     * @param  array<string, mixed>  $guzzle_options
     */
    public function __construct(
        string $api_key = 'fake-api-key',
        string $base_url = self::DEFAULT_BASE_URL,
        string $api_version = self::DEFAULT_API_VERSION,
        array $guzzle_options = []
    ) {
        parent::__construct($api_key, $base_url, $api_version, $guzzle_options);
    }

    /**
     * @param  array<string, mixed>  $guzzle_options
     */
    protected function initClient(array $guzzle_options): Client
    {
        $stack = HandlerStack::create(function (RequestInterface $request, array $options) {
            $response = array_shift($this->response_queue) ?? new GuzzleResponse(200, [], '{}');

            return Create::promiseFor($response);
        });

        $stack->push(Middleware::history($this->history));

        $guzzle_client = new GuzzleClient(array_merge([
            'base_uri' => $this->baseUri(),
            'handler' => $stack,
        ], $guzzle_options));

        return new Client($guzzle_client);
    }

    /**
     * Queue a fake JSON response to be returned by the next call made through
     * this client. Calls made after the queue is exhausted get an empty
     * 200 OK JSON response.
     *
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     */
    public function fakeResponse(array $body = [], int $status = 200, array $headers = []): static
    {
        $this->response_queue[] = new GuzzleResponse($status, $headers, (string) json_encode($body));

        return $this;
    }

    /**
     * All requests sent through this fake client so far, in order.
     *
     * @return array<int, RequestInterface>
     */
    public function sent(): array
    {
        return array_map(static fn (array $entry) => $entry['request'], $this->history);
    }

    /**
     * @param  callable(RequestInterface): bool  $callback
     */
    public function assertSent(callable $callback): void
    {
        foreach ($this->sent() as $request) {
            if ($callback($request)) {
                PHPUnit::assertTrue(true);

                return;
            }
        }

        PHPUnit::fail('The expected DhivehiGPT API request was not sent.');
    }

    /**
     * @param  callable(RequestInterface): bool  $callback
     */
    public function assertNotSent(callable $callback): void
    {
        foreach ($this->sent() as $request) {
            if ($callback($request)) {
                PHPUnit::fail('An unexpected DhivehiGPT API request was sent.');
            }
        }

        PHPUnit::assertTrue(true);
    }

    public function assertNothingSent(): void
    {
        PHPUnit::assertEmpty($this->sent(), 'DhivehiGPT API requests were sent when none were expected.');
    }

    public function assertSentCount(int $count): void
    {
        PHPUnit::assertCount($count, $this->sent());
    }
}
