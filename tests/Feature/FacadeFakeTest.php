<?php

namespace Javaabu\DhivehiGpt\Tests\Feature;

use Javaabu\DhivehiGpt\DhivehiGpt as DhivehiGptClient;
use Javaabu\DhivehiGpt\Facades\DhivehiGpt;
use Javaabu\DhivehiGpt\Testing\DhivehiGptFake;
use Javaabu\DhivehiGpt\Tests\TestCase;

class FacadeFakeTest extends TestCase
{
    public function test_fake_swaps_the_container_binding(): void
    {
        DhivehiGpt::fake();

        $this->assertInstanceOf(DhivehiGptFake::class, $this->app->make(DhivehiGptClient::class));
    }

    public function test_fake_returns_the_fake_instance(): void
    {
        $fake = DhivehiGpt::fake();

        $this->assertInstanceOf(DhivehiGptFake::class, $fake);
    }

    public function test_fake_seeds_the_queue_with_the_given_responses(): void
    {
        DhivehiGpt::fake([
            ['data' => ['uuid' => 'first']],
            ['data' => ['uuid' => 'second']],
        ]);

        $this->assertSame(['data' => ['uuid' => 'first']], DhivehiGpt::tts()->get('a'));
        $this->assertSame(['data' => ['uuid' => 'second']], DhivehiGpt::tts()->get('b'));
    }

    public function test_facade_calls_are_forwarded_to_the_fake(): void
    {
        DhivehiGpt::fake()->fakeResponse(['data' => ['uuid' => 'test-uuid']]);

        $audio = DhivehiGpt::tts()->generate('Hello', 'hajja');

        $this->assertSame('test-uuid', $audio['data']['uuid']);
    }

    public function test_assertions_work_through_the_facade(): void
    {
        DhivehiGpt::fake();

        DhivehiGpt::tts()->generate('Hello', 'hajja');

        DhivehiGpt::assertSent(fn ($request) => str_contains((string) $request->getUri(), '/tts'));
        DhivehiGpt::assertSentCount(1);
    }

    public function test_container_resolved_instances_share_the_fake(): void
    {
        DhivehiGpt::fake()->fakeResponse(['data' => ['uuid' => 'test-uuid']]);

        $audio = $this->app->make(DhivehiGptClient::class)->tts()->generate('Hello', 'hajja');

        $this->assertSame('test-uuid', $audio['data']['uuid']);
        DhivehiGpt::assertSentCount(1);
    }
}
