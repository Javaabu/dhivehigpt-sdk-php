# DhivehiGPT PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/javaabu/dhivehigpt-sdk.svg?style=flat-square)](https://packagist.org/packages/javaabu/dhivehigpt-sdk)
[![Test Status](../../actions/workflows/run-tests.yml/badge.svg)](../../actions/workflows/run-tests.yml)
![Code Coverage Badge](./.github/coverage.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/javaabu/dhivehigpt-sdk.svg?style=flat-square)](https://packagist.org/packages/javaabu/dhivehigpt-sdk)

A lightweight PHP / Laravel SDK for the [DhivehiGPT API](https://dhivehigpt.com/docs), covering voices, text-to-speech, subscription balance, and tasks. It has no framework dependency, so it works in plain PHP applications as well as any Laravel application from version 9 through 13. Its only dependency is [Guzzle](https://docs.guzzlephp.org), the standard PHP HTTP client — already installed in most Laravel and modern PHP projects.

## Installation

You can install the package via composer:

```bash
composer require javaabu/dhivehigpt-sdk
```

## Getting an API key

You need a DhivehiGPT team account with an **active subscription** to generate API keys, and only **team owners and admins** can generate them. Generate one from the [API Keys](https://dhivehigpt.com/team/api-keys) page in your DhivehiGPT team account.

## Basic usage

### Plain PHP

```php
use Javaabu\DhivehiGpt\DhivehiGpt;

$dhivehigpt = new DhivehiGpt('YOUR_API_KEY');

$voices = $dhivehigpt->voices()->list();

$audio = $dhivehigpt->tts()->generate('ދިވެހިޖީޕީޓީއަށް މަރުޙަބާ', 'hajja');
```

### Laravel

The package's configuration is read from the `dhivehigpt` key in Laravel's built-in `config/services.php` file — there is no separate config file to publish. Add the following:

```php
'dhivehigpt' => [
    'api_key' => env('DHIVEHIGPT_API_KEY'),
    'base_url' => env('DHIVEHIGPT_BASE_URL'), // optional
    'api_version' => env('DHIVEHIGPT_API_VERSION'), // optional
    'guzzle' => [], // optional, additional Guzzle client options
],
```

Then set your API key in `.env`:

```
DHIVEHIGPT_API_KEY=sk-dvgpt-xxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxxxxx
```

The service provider and the `DhivehiGpt` facade are auto-discovered. Use the facade, or inject `Javaabu\DhivehiGpt\DhivehiGpt`:

```php
use Javaabu\DhivehiGpt\Facades\DhivehiGpt;

$voices = DhivehiGpt::voices()->list();

$audio = DhivehiGpt::tts()->generate('ދިވެހިޖީޕީޓީއަށް މަރުޙަބާ', 'hajja');
```

See [Installation & Setup](https://docs.javaabu.com/docs/dhivehigpt-sdk-php/installation-and-setup) for the full details, including how to customize the underlying Guzzle client.

## Documentation

You'll find the full documentation on [https://docs.javaabu.com/docs/dhivehigpt-sdk-php](https://docs.javaabu.com/docs/dhivehigpt-sdk-php).

Find yourself stuck using the package? Found a bug? Do you have general questions or suggestions for improving this package? Feel free to create an [issue](../../issues) on GitHub, we'll try to address it as soon as possible.

If you've found a bug regarding security please mail [info@javaabu.com](mailto:info@javaabu.com) instead of using the issue tracker.


## Testing

You can run the tests with

``` bash
./vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [info@javaabu.com](mailto:info@javaabu.com) instead of using the issue tracker.

## Credits

- [Javaabu Pvt. Ltd.](https://github.com/javaabu)
- [Arushad Ahmed (@dash8x)](http://arushad.com)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
