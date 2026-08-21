---
title: Text-to-Speech (TTS)
sidebar_position: 4
---

Generate and manage API-created Text-to-Speech (TTS) audios, via `$dhivehigpt->tts()`.

## List TTS audios

Returns the audios created by the current API key's team (including those created by other API keys belonging to the same team):

```php
$audios = $dhivehigpt->tts()->list([
    'search' => 'welcome',
    'voice' => 'hajja',
    'date_from' => '2026-01-01',
    'date_to' => '2026-12-31',
    'sort' => '-created_at', // created_at | -created_at | updated_at | -updated_at | uuid | -uuid
    'per_page' => 20,
    'page' => 1,
]);
```

## Generate a TTS audio

```php
$audio = $dhivehigpt->tts()->generate(
    text: 'ދިވެހިޖީޕީޓީއަށް މަރުޙަބާ',
    voice: 'hajja',
);

echo $audio['data']['audio_url'];
```

`text` must not be greater than 10,000 characters, and `voice` must be a valid voice slug (see [Voices](./voices)). This charges the API key's team for the usage — Guzzle throws a `GuzzleHttp\Exception\ClientException` if the team has no credits available (HTTP 403). See [Error Handling](./error-handling).

Audio generation can take a while for longer text, so `generate()` defaults to a 120 second timeout, longer than every other call the SDK makes. Pass a `timeout` (in seconds) to override it, e.g. `$dhivehigpt->tts()->generate($text, $voice, timeout: 180.0)`.

If the API adds a request body field this method doesn't have an argument for yet, pass it via `$body` — `text`/`voice` always win over a conflicting key in `$body`:

```php
$audio = $dhivehigpt->tts()->generate('Hello', 'hajja', body: ['speed' => 1.2]);
```

## Get a TTS audio

```php
$audio = $dhivehigpt->tts()->get('c6fc95bf-3f1d-4202-b31e-3e79753a1ef5');
```

## Update a TTS audio

Only the title can be updated:

```php
$audio = $dhivehigpt->tts()->update('c6fc95bf-3f1d-4202-b31e-3e79753a1ef5', 'A new title');
```

Like `generate()`, `update()` also accepts a `$body` array for any additional request body fields, with `title` always taking precedence.

## Delete a TTS audio

```php
$dhivehigpt->tts()->delete('c6fc95bf-3f1d-4202-b31e-3e79753a1ef5');
```

Get, update, and delete all let Guzzle throw a `GuzzleHttp\Exception\ClientException` (HTTP 404) when the TTS audio was not created through the API, does not exist, or does not belong to the current API key's team.

## Overriding the timeout

Every module method accepts an optional `timeout` (in seconds) as its last argument. Pass `null` (the default) to use the client's configured default, or a float to override it for that one call:

```php
$dhivehigpt->tts()->list(timeout: 5.0);
```
