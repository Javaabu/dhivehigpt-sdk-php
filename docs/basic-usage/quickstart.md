---
title: Quickstart
sidebar_position: 1
---

Create a client with your API key, then call any of the four resources: `voices()`, `tts()`, `balance()`, and `tasks()`.

```php
use Javaabu\DhivehiGpt\DhivehiGpt;

$dhivehigpt = new DhivehiGpt('YOUR_API_KEY');

// List the available voices
$voices = $dhivehigpt->voices()->list();

// Generate a text-to-speech audio
$audio = $dhivehigpt->tts()->generate('ދިވެހިޖީޕީޓީއަށް މަރުޙަބާ', 'hajja');

echo $audio['data']['audio_url'];
```

Every resource method returns a plain associative array, decoded directly from the API's JSON response — the same `data` / `links` / `meta` shape documented at [dhivehigpt.com/docs](https://dhivehigpt.com/docs). Nothing is wrapped in SDK-specific objects, so the array keys always match the API documentation exactly.

If the API returns an error, the SDK throws an exception instead of returning an array. See [Error Handling](./error-handling) for the full list of exceptions.

In a Laravel application, use the `DhivehiGpt` facade instead — see [Laravel Usage](./laravel-usage).
