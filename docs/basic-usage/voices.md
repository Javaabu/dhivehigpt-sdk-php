---
title: Voices
sidebar_position: 3
---

Browse the available Text-to-Speech (TTS) voices available for audio generation, via `$dhivehigpt->voices()`.

## List voices

```php
$voices = $dhivehigpt->voices()->list();
```

Results are paginated and can be filtered and sorted by passing an array of query parameters:

```php
$voices = $dhivehigpt->voices()->list([
    'search' => 'haj',
    'gender' => 'female', // female | male
    'age_group' => 'thirties', // ones | tens | twenties | thirties | fourties | fifties | sixties | seventies
    'is_premium' => false,
    'sort' => '-name', // name | -name | slug | -slug | id | -id | order_column | -order_column
    'per_page' => 20,
    'page' => 1,
]);
```

## Get a voice

```php
$voice = $dhivehigpt->voices()->get('hajja');

echo $voice['data']['audio_sample_url'];
```

Guzzle throws a `GuzzleHttp\Exception\ClientException` (HTTP 404) if no voice exists with the supplied slug — see [Error Handling](./error-handling).
