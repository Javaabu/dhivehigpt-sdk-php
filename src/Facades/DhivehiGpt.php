<?php

namespace Javaabu\DhivehiGpt\Facades;

use Illuminate\Support\Facades\Facade;
use Javaabu\DhivehiGpt\DhivehiGpt as DhivehiGptClient;
use Javaabu\DhivehiGpt\Modules\BalanceModule;
use Javaabu\DhivehiGpt\Modules\TasksModule;
use Javaabu\DhivehiGpt\Modules\TtsModule;
use Javaabu\DhivehiGpt\Modules\VoicesModule;
use Javaabu\DhivehiGpt\Testing\DhivehiGptFake;

/**
 * @method static VoicesModule voices()
 * @method static TtsModule tts()
 * @method static BalanceModule balance()
 * @method static TasksModule tasks()
 * @method static DhivehiGptFake fakeResponse(array $body = [], int $status = 200, array $headers = [])
 * @method static array<int, \Psr\Http\Message\RequestInterface> sent()
 * @method static void assertSent(callable $callback)
 * @method static void assertNotSent(callable $callback)
 * @method static void assertNothingSent()
 * @method static void assertSentCount(int $count)
 *
 * @see \Javaabu\DhivehiGpt\DhivehiGpt
 * @see \Javaabu\DhivehiGpt\Testing\DhivehiGptFake
 */
class DhivehiGpt extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DhivehiGptClient::class;
    }

    /**
     * Swap the underlying client for a fake that records every request and
     * returns queued canned responses instead of hitting the real API.
     *
     * @param  array<int, array<string, mixed>>  $responses  Response bodies to queue immediately,
     *                                                        each returned as a 200 OK JSON response.
     *                                                        Queue more later with fakeResponse().
     */
    public static function fake(array $responses = []): DhivehiGptFake
    {
        $fake = new DhivehiGptFake();

        foreach ($responses as $response) {
            $fake->fakeResponse($response);
        }

        static::swap($fake);

        return $fake;
    }
}
