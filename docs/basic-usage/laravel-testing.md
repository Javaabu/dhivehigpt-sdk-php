---
title: Testing in Laravel
sidebar_position: 2.5
---

When testing code that calls the DhivehiGPT API, you don't want your test suite making real HTTP requests. Use `DhivehiGpt::fake()`.

## Faking responses

`DhivehiGpt::fake()` swaps the client bound in the container for a `Javaabu\DhivehiGpt\Testing\DhivehiGptFake` — it records every request and returns queued responses instead of hitting the real API. Calling it also swaps `app(\Javaabu\DhivehiGpt\DhivehiGpt::class)` everywhere it's resolved, not just through the facade.

```php
use Javaabu\DhivehiGpt\Facades\DhivehiGpt;
use Tests\TestCase;

class TtsGenerationTest extends TestCase
{
    public function test_it_generates_a_tts_audio(): void
    {
        DhivehiGpt::fake()->fakeResponse([
            'data' => ['uuid' => 'test-uuid', 'audio_url' => 'https://example.com/test.wav'],
        ]);

        $audio = DhivehiGpt::tts()->generate('Hello', 'hajja');

        $this->assertSame('test-uuid', $audio['data']['uuid']);
    }
}
```

You can also seed the queue directly in the `fake()` call, which is handy when a test makes more than one call — each response is returned in order:

```php
DhivehiGpt::fake([
    ['data' => ['uuid' => 'first']],
    ['data' => ['uuid' => 'second']],
]);
```

`fakeResponse()` also accepts a status code and headers, for testing how your code handles an error — Guzzle will throw its own exception for a non-2xx status, exactly like it does against the real API (see [Error Handling](./error-handling)):

```php
DhivehiGpt::fake()->fakeResponse(
    body: ['message' => "You don't have any credits available."],
    status: 403,
);
```

If a call is made after the queue runs out, the fake returns an empty `200 OK` response rather than throwing, so you only need to queue responses for the calls your test actually cares about.

## Asserting what was sent

The fake records every request, and exposes assertions against them:

```php
DhivehiGpt::fake();

DhivehiGpt::tts()->generate('Hello', 'hajja');

DhivehiGpt::assertSent(fn ($request) => str_contains((string) $request->getUri(), '/tts'));
DhivehiGpt::assertSentCount(1);
```

| Method | Checks |
|---|---|
| `assertSent(callable $callback)` | At least one request matches — `$callback` receives a `Psr\Http\Message\RequestInterface` and returns a bool. |
| `assertNotSent(callable $callback)` | No request matches. |
| `assertNothingSent()` | No requests were sent at all. |
| `assertSentCount(int $count)` | Exactly `$count` requests were sent. |
| `sent()` | Returns every sent request, as an array of `RequestInterface`, if you need a custom assertion. |

## Testing without Laravel

`DhivehiGptFake` works standalone too, so plain PHP tests get the same faking without the facade:

```php
use Javaabu\DhivehiGpt\Testing\DhivehiGptFake;

$dhivehigpt = new DhivehiGptFake();
$dhivehigpt->fakeResponse(['data' => ['uuid' => 'test-uuid']]);

$audio = $dhivehigpt->tts()->generate('Hello', 'hajja');

$dhivehigpt->assertSent(fn ($request) => str_contains((string) $request->getUri(), '/tts'));
```

## Mocking the client directly

If you'd rather not exercise the SDK's internals at all — not even the fake's request/response cycle — swap the container binding with a plain mock. `DhivehiGpt::voices()`/`tts()`/`balance()`/`tasks()` each return a Module instance, so mock that instead:

```php
use Javaabu\DhivehiGpt\DhivehiGpt;
use Javaabu\DhivehiGpt\Modules\TtsModule;
use Tests\TestCase;

class TtsGenerationTest extends TestCase
{
    public function test_it_generates_a_tts_audio(): void
    {
        $tts = \Mockery::mock(TtsModule::class);
        $tts->shouldReceive('generate')
            ->once()
            ->with('Hello', 'hajja')
            ->andReturn(['data' => ['uuid' => 'test-uuid']]);

        $this->mock(DhivehiGpt::class, function ($mock) use ($tts) {
            $mock->shouldReceive('tts')->andReturn($tts);
        });

        // ... call the code under test, which resolves DhivehiGpt::class or the DhivehiGpt facade
    }
}
```
