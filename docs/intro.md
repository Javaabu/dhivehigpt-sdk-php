---
title: Introduction
sidebar_position: 1.0
---

# DhivehiGPT PHP SDK

[DhivehiGPT PHP SDK](https://github.com/Javaabu/dhivehigpt-sdk-php) is a lightweight PHP / Laravel SDK for the [DhivehiGPT API](https://dhivehigpt.com/docs).

## Key Features

- **Framework agnostic**: works in any PHP 8.0+ application, with or without Laravel.
- **Laravel first-class support**: a service provider, a `DhivehiGpt` facade, and configuration via `config/services.php`.
- **Minimal dependencies**: built on [Guzzle](https://docs.guzzlephp.org), the standard PHP HTTP client — already installed in most Laravel and modern PHP projects.
- **Full API coverage**: voices, text-to-speech, subscription balance, and tasks.
- **No exception wrapping**: API errors surface as Guzzle's own exceptions, so error handling is the same as any other Guzzle-based code.

## Quick Example

```php
use Javaabu\DhivehiGpt\DhivehiGpt;

$dhivehigpt = new DhivehiGpt('YOUR_API_KEY');

$audio = $dhivehigpt->tts()->generate('ދިވެހިޖީޕީޓީއަށް މަރުޙަބާ', 'hajja');
```

## Next Steps

Ready to get started?

1. Head over to the [Installation & Setup](./installation-and-setup) guide to add the package to your project and generate an API key.
2. Explore the [Basic Usage](./basic-usage/quickstart) section to understand the core resources.
