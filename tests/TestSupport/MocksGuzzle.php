<?php

namespace Javaabu\DhivehiGpt\Tests\TestSupport;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Javaabu\DhivehiGpt\Http\Client;
use Psr\Http\Message\RequestInterface;

/**
 * Wraps a Guzzle MockHandler in our Http\Client, so tests can assert on
 * the exact outgoing request without making any real network calls.
 */
trait MocksGuzzle
{
    /**
     * @var array<int, array{request: RequestInterface, response: mixed, options: array<string, mixed>}>
     */
    protected array $guzzle_history = [];

    /**
     * @param  array<int, \GuzzleHttp\Psr7\Response|\Throwable>  $queue
     * @param  array<string, mixed>  $options
     */
    protected function makeClient(array $queue, array $options = []): Client
    {
        $this->guzzle_history = [];

        $mock_handler = new MockHandler($queue);

        $stack = HandlerStack::create($mock_handler);
        $stack->push(Middleware::history($this->guzzle_history));

        $guzzle_client = new GuzzleClient(array_merge([
            'base_uri' => 'https://api.dhivehigpt.com/v1/',
            'handler' => $stack,
        ], $options));

        return new Client($guzzle_client);
    }

    protected function lastRequest(): ?RequestInterface
    {
        if (empty($this->guzzle_history)) {
            return null;
        }

        return $this->guzzle_history[count($this->guzzle_history) - 1]['request'];
    }

    /**
     * @return array<string, mixed>
     */
    protected function lastRequestOptions(): array
    {
        if (empty($this->guzzle_history)) {
            return [];
        }

        return $this->guzzle_history[count($this->guzzle_history) - 1]['options'];
    }
}
