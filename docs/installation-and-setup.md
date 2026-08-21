---
title: Installation & Setup
sidebar_position: 1.2
---

## Installing the package

You can install the package via composer:

```bash
composer require javaabu/dhivehigpt-sdk
```

## Getting an API key

You need a DhivehiGPT team account with an **active subscription** to generate API keys, and only **team owners and admins** are able to generate them.

Generate an API key from the [API Keys](https://dhivehigpt.com/team/api-keys) page in your DhivehiGPT team account.

:::danger

Treat your API key like a password. Never commit it to source control or expose it in client-side code — keep it in an environment variable.

:::

## Configuring a plain PHP application

Instantiate the `Javaabu\DhivehiGpt\DhivehiGpt` client directly with your API key:

```php
use Javaabu\DhivehiGpt\DhivehiGpt;

$dhivehigpt = new DhivehiGpt('YOUR_API_KEY');
```

The base URL, API version, and Guzzle options are all optional. The client defaults to the production API (`https://api.dhivehigpt.com`) and API version `v1`:

```php
use Javaabu\DhivehiGpt\DhivehiGpt;

$dhivehigpt = new DhivehiGpt(
    api_key: 'YOUR_API_KEY',
    base_url: 'https://api.dhivehigpt.com', // optional
    api_version: 'v1', // optional
    guzzle_options: [], // optional, see below
);
```

### Customizing the underlying Guzzle client

The SDK is built on [Guzzle](https://docs.guzzlephp.org). The last constructor argument, `guzzle_options`, is merged over the SDK's own defaults (`base_uri`, the `X-Api-Key`/`Accept` headers, and timeouts) when constructing the client, so you can pass any [Guzzle request option](https://docs.guzzlephp.org/en/stable/request-options.html) — a proxy, TLS options, extra default headers, or a longer timeout:

```php
$dhivehigpt = new DhivehiGpt('YOUR_API_KEY', guzzle_options: [
    'proxy' => 'http://localhost:8125',
    'timeout' => 90.0,
]);
```

## Configuring a Laravel application

The package's configuration is read from the `dhivehigpt` key in Laravel's built-in `config/services.php` file, alongside your other third-party service credentials. There is no separate config file to publish.

Add the following to `config/services.php`:

```php
'dhivehigpt' => [
    'api_key' => env('DHIVEHIGPT_API_KEY'),
    'base_url' => env('DHIVEHIGPT_BASE_URL'), // optional
    'api_version' => env('DHIVEHIGPT_API_VERSION'), // optional
    'guzzle' => [], // optional, additional Guzzle client options — see above
],
```

Then set your API key in `.env`:

```
DHIVEHIGPT_API_KEY=sk-dvgpt-xxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxxxxx
```

The service provider and the `DhivehiGpt` facade are auto-discovered, so no further setup is required. See [Laravel Usage](./basic-usage/laravel-usage) for how to use them.
