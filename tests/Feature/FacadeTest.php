<?php

namespace Javaabu\DhivehiGpt\Tests\Feature;

use Javaabu\DhivehiGpt\DhivehiGpt as DhivehiGptClient;
use Javaabu\DhivehiGpt\Facades\DhivehiGpt;
use Javaabu\DhivehiGpt\Modules\BalanceModule;
use Javaabu\DhivehiGpt\Modules\TasksModule;
use Javaabu\DhivehiGpt\Modules\TtsModule;
use Javaabu\DhivehiGpt\Modules\VoicesModule;
use Javaabu\DhivehiGpt\Tests\TestCase;

class FacadeTest extends TestCase
{
    public function test_it_resolves_to_the_client_bound_in_the_container(): void
    {
        $this->assertSame(
            $this->app->make(DhivehiGptClient::class),
            DhivehiGpt::getFacadeRoot()
        );
    }

    public function test_it_forwards_calls_to_the_underlying_client(): void
    {
        $this->assertInstanceOf(VoicesModule::class, DhivehiGpt::voices());
        $this->assertInstanceOf(TtsModule::class, DhivehiGpt::tts());
        $this->assertInstanceOf(BalanceModule::class, DhivehiGpt::balance());
        $this->assertInstanceOf(TasksModule::class, DhivehiGpt::tasks());
    }
}
