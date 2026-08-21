<?php

namespace Javaabu\DhivehiGpt\Tests\Unit\Testing;

use Javaabu\DhivehiGpt\Testing\DhivehiGptFake;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

class DhivehiGptFakeTest extends TestCase
{
    public function test_it_can_be_constructed_without_any_arguments(): void
    {
        $fake = new DhivehiGptFake();

        $this->assertInstanceOf(DhivehiGptFake::class, $fake);
    }

    public function test_it_returns_an_empty_response_by_default(): void
    {
        $fake = new DhivehiGptFake();

        $result = $fake->balance()->get();

        $this->assertSame([], $result);
    }

    public function test_it_returns_queued_responses_in_order(): void
    {
        $fake = new DhivehiGptFake();
        $fake->fakeResponse(['data' => ['uuid' => 'first']]);
        $fake->fakeResponse(['data' => ['uuid' => 'second']]);

        $this->assertSame(['data' => ['uuid' => 'first']], $fake->tts()->get('a'));
        $this->assertSame(['data' => ['uuid' => 'second']], $fake->tts()->get('b'));
    }

    public function test_it_falls_back_to_an_empty_response_once_the_queue_is_exhausted(): void
    {
        $fake = new DhivehiGptFake();
        $fake->fakeResponse(['data' => ['uuid' => 'first']]);

        $fake->tts()->get('a');
        $result = $fake->tts()->get('b');

        $this->assertSame([], $result);
    }

    public function test_sent_records_every_request(): void
    {
        $fake = new DhivehiGptFake();

        $fake->voices()->list();
        $fake->tts()->generate('hello', 'hajja');

        $this->assertCount(2, $fake->sent());
        $this->assertSame('GET', $fake->sent()[0]->getMethod());
        $this->assertSame('POST', $fake->sent()[1]->getMethod());
    }

    public function test_assert_sent_passes_when_a_matching_request_was_sent(): void
    {
        $fake = new DhivehiGptFake();
        $fake->tts()->generate('hello', 'hajja');

        $fake->assertSent(fn ($request) => str_contains((string) $request->getUri(), '/tts'));
    }

    public function test_assert_sent_fails_when_no_matching_request_was_sent(): void
    {
        $fake = new DhivehiGptFake();
        $fake->voices()->list();

        $this->expectException(AssertionFailedError::class);

        $fake->assertSent(fn ($request) => str_contains((string) $request->getUri(), '/tts'));
    }

    public function test_assert_not_sent_passes_when_no_matching_request_was_sent(): void
    {
        $fake = new DhivehiGptFake();
        $fake->voices()->list();

        $fake->assertNotSent(fn ($request) => str_contains((string) $request->getUri(), '/tts'));
    }

    public function test_assert_not_sent_fails_when_a_matching_request_was_sent(): void
    {
        $fake = new DhivehiGptFake();
        $fake->tts()->generate('hello', 'hajja');

        $this->expectException(AssertionFailedError::class);

        $fake->assertNotSent(fn ($request) => str_contains((string) $request->getUri(), '/tts'));
    }

    public function test_assert_nothing_sent_passes_when_no_requests_were_sent(): void
    {
        $fake = new DhivehiGptFake();

        $fake->assertNothingSent();
    }

    public function test_assert_nothing_sent_fails_when_a_request_was_sent(): void
    {
        $fake = new DhivehiGptFake();
        $fake->voices()->list();

        $this->expectException(AssertionFailedError::class);

        $fake->assertNothingSent();
    }

    public function test_assert_sent_count_passes_for_the_correct_count(): void
    {
        $fake = new DhivehiGptFake();
        $fake->voices()->list();
        $fake->balance()->get();

        $fake->assertSentCount(2);
    }

    public function test_assert_sent_count_fails_for_the_wrong_count(): void
    {
        $fake = new DhivehiGptFake();
        $fake->voices()->list();

        $this->expectException(AssertionFailedError::class);

        $fake->assertSentCount(2);
    }
}
