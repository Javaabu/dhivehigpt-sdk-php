<?php

namespace Javaabu\DhivehiGpt\Http;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A thin wrapper around a pre-configured Guzzle client (base URI, auth
 * header, and timeouts are all set up by the caller). Guzzle is left to
 * raise its own exceptions for non-2xx responses and transport failures —
 * catch GuzzleHttp\Exception\GuzzleException (or its more specific
 * subclasses) to handle them.
 */
class Client
{
    protected ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = [], ?float $timeout = null): array
    {
        $query = array_filter($query, static fn ($value) => $value !== null);

        return $this->send('GET', $path, ['query' => $query], $timeout);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function post(string $path, array $body = [], ?float $timeout = null): array
    {
        return $this->send('POST', $path, ['json' => $body], $timeout);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function put(string $path, array $body = [], ?float $timeout = null): array
    {
        return $this->send('PUT', $path, ['json' => $body], $timeout);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $path, ?float $timeout = null): array
    {
        return $this->send('DELETE', $path, [], $timeout);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    protected function send(string $method, string $path, array $options, ?float $timeout): array
    {
        if ($timeout !== null) {
            $options['timeout'] = $timeout;
        }

        $response = $this->client->request($method, ltrim($path, '/'), $options);

        return $this->decode($response);
    }

    /**
     * @return array<string, mixed>
     */
    protected function decode(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        if (trim($body) === '') {
            return [];
        }

        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : [];
    }
}
