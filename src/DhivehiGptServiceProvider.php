<?php

namespace Javaabu\DhivehiGpt;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class DhivehiGptServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton(DhivehiGpt::class, function (Application $app) {
            $config = (array) $app['config']->get('services.dhivehigpt', []);

            $api_key = (string) ($config['api_key'] ?? '');
            $base_url = (string) ($config['base_url'] ?? DhivehiGpt::DEFAULT_BASE_URL);
            $api_version = (string) ($config['api_version'] ?? DhivehiGpt::DEFAULT_API_VERSION);
            $guzzle_options = (array) ($config['guzzle'] ?? []);

            return new DhivehiGpt($api_key, $base_url, $api_version, $guzzle_options);
        });

        $this->app->alias(DhivehiGpt::class, 'dhivehigpt');
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [DhivehiGpt::class, 'dhivehigpt'];
    }
}
