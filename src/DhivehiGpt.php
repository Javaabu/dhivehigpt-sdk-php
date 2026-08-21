<?php

namespace Javaabu\DhivehiGpt;

use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use Javaabu\DhivehiGpt\Http\Client;
use Javaabu\DhivehiGpt\Modules\BalanceModule;
use Javaabu\DhivehiGpt\Modules\TasksModule;
use Javaabu\DhivehiGpt\Modules\TtsModule;
use Javaabu\DhivehiGpt\Modules\VoicesModule;

/**
 * The DhivehiGPT API client.
 *
 * Works standalone in any PHP 8.0+ application, or via the bundled
 * service provider and facade in a Laravel application.
 */
class DhivehiGpt
{
    public const DEFAULT_BASE_URL = 'https://api.dhivehigpt.com';

    public const DEFAULT_API_VERSION = 'v1';

    /**
     * The default request timeout, in seconds. Individual modules override
     * this per-request where an operation is documented to take longer,
     * e.g. TtsModule::GENERATION_TIMEOUT.
     */
    public const DEFAULT_TIMEOUT = 30.0;

    public const DEFAULT_CONNECT_TIMEOUT = 10.0;

    protected string $api_key;

    protected string $base_url;

    protected string $api_version;

    protected Client $client;

    protected VoicesModule $voices;

    protected TtsModule $tts;

    protected BalanceModule $balance;

    protected TasksModule $tasks;

    /**
     * @param  string  $api_key  The API key generated from the DhivehiGPT team account.
     * @param  string  $base_url  The base URL of the DhivehiGPT API. Defaults to the production API.
     * @param  string  $api_version  The API version path segment. Defaults to "v1".
     * @param  array<string, mixed>  $guzzle_options  Additional options merged into the underlying Guzzle
     *                                                 client, e.g. 'proxy', 'verify', 'headers', or a custom
     *                                                 'handler' for testing.
     *
     * @throws InvalidArgumentException if the API key is empty.
     */
    public function __construct(
        string $api_key,
        string $base_url = self::DEFAULT_BASE_URL,
        string $api_version = self::DEFAULT_API_VERSION,
        array $guzzle_options = []
    ) {
        if (trim($api_key) === '') {
            throw new InvalidArgumentException('A DhivehiGPT API key is required.');
        }

        $this->api_key = $api_key;
        $this->base_url = $base_url;
        $this->api_version = $api_version;

        $this->client = $this->initClient($guzzle_options);

        $this->voices = new VoicesModule($this->client);
        $this->tts = new TtsModule($this->client);
        $this->balance = new BalanceModule($this->client);
        $this->tasks = new TasksModule($this->client);
    }

    /**
     * Build the HTTP client, configuring the Guzzle base URI, auth header,
     * and timeouts, with $guzzle_options merged on top. Override this to
     * customize how the underlying Guzzle client is constructed.
     *
     * @param  array<string, mixed>  $guzzle_options
     */
    protected function initClient(array $guzzle_options): Client
    {
        $default_options = [
            'base_uri' => $this->baseUri(),
            'headers' => [
                'Accept' => 'application/json',
                'X-Api-Key' => $this->api_key,
            ],
            'timeout' => self::DEFAULT_TIMEOUT,
            'connect_timeout' => self::DEFAULT_CONNECT_TIMEOUT,
        ];

        $guzzle_client = new GuzzleClient(array_replace_recursive($default_options, $guzzle_options));

        return new Client($guzzle_client);
    }

    /**
     * The base URI requests are resolved against, combining the base URL and API version.
     */
    protected function baseUri(): string
    {
        return rtrim($this->base_url, '/').'/'.trim($this->api_version, '/').'/';
    }

    /**
     * Browse the available Text-to-Speech (TTS) voices.
     */
    public function voices(): VoicesModule
    {
        return $this->voices;
    }

    /**
     * Generate and manage API-created Text-to-Speech (TTS) audios.
     */
    public function tts(): TtsModule
    {
        return $this->tts;
    }

    /**
     * View the subscription and credit balance associated with the current API key team.
     */
    public function balance(): BalanceModule
    {
        return $this->balance;
    }

    /**
     * Browse available tasks and calculate their credit cost.
     */
    public function tasks(): TasksModule
    {
        return $this->tasks;
    }
}
