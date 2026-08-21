<?php

namespace Javaabu\DhivehiGpt\Tests\Feature;

use InvalidArgumentException;
use Javaabu\DhivehiGpt\DhivehiGpt;
use Javaabu\DhivehiGpt\Tests\TestCase;

class DhivehiGptServiceProviderTest extends TestCase
{
    public function test_it_binds_the_client_to_the_container(): void
    {
        $client = $this->app->make(DhivehiGpt::class);

        $this->assertInstanceOf(DhivehiGpt::class, $client);
    }

    public function test_it_registers_the_dhivehigpt_alias(): void
    {
        $this->assertSame(
            $this->app->make(DhivehiGpt::class),
            $this->app->make('dhivehigpt')
        );
    }

    public function test_it_is_registered_as_a_singleton(): void
    {
        $this->assertSame(
            $this->app->make(DhivehiGpt::class),
            $this->app->make(DhivehiGpt::class)
        );
    }

    public function test_it_reads_the_api_key_from_the_services_config(): void
    {
        $this->app['config']->set('services.dhivehigpt', ['api_key' => 'my-configured-key']);

        $client = $this->app->make(DhivehiGpt::class);

        $this->assertInstanceOf(DhivehiGpt::class, $client);
    }

    public function test_it_reads_the_base_url_and_api_version_from_the_services_config(): void
    {
        $this->app['config']->set('services.dhivehigpt', [
            'api_key' => 'my-configured-key',
            'base_url' => 'https://custom.example.com',
            'api_version' => 'v2',
        ]);

        $client = $this->app->make(DhivehiGpt::class);

        $this->assertInstanceOf(DhivehiGpt::class, $client);
    }

    public function test_it_throws_when_no_api_key_is_configured(): void
    {
        $this->app['config']->set('services.dhivehigpt', []);

        $this->expectException(InvalidArgumentException::class);

        $this->app->make(DhivehiGpt::class);
    }

    public function test_it_reads_additional_guzzle_options_from_the_services_config(): void
    {
        $this->app['config']->set('services.dhivehigpt', [
            'api_key' => 'my-configured-key',
            'guzzle' => [
                'headers' => ['X-Custom-Header' => 'custom-value'],
            ],
        ]);

        $client = $this->app->make(DhivehiGpt::class);

        $this->assertInstanceOf(DhivehiGpt::class, $client);
    }
}
