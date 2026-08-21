---
title: Tasks
sidebar_position: 6
---

Browse the available tasks and calculate their credit cost, via `$dhivehigpt->tasks()`.

## List tasks

```php
$tasks = $dhivehigpt->tasks()->list([
    'search' => 'audio',
    'platform' => 'tts', // chat | stt | tts | ocr
    'sort' => 'order_column', // name | -name | order_column | -order_column | slug | -slug
    'per_page' => 20,
    'page' => 1,
]);
```

## Get a task

```php
$task = $dhivehigpt->tasks()->get('audio_generation');
```

## Calculate a task's cost

Calculate how many credits a given number of usage units would cost, before performing the underlying action:

```php
$calculation = $dhivehigpt->tasks()->calculate('audio_generation', 1000);

if ($calculation['can_proceed']) {
    echo "This would cost {$calculation['charged_credits']} credits.";
}
```

Guzzle throws a `GuzzleHttp\Exception\ClientException` (HTTP 422) if the supplied task slug does not identify an available task — see [Error Handling](./error-handling).

`calculate()` also accepts a `$body` array (as its third argument) for any additional request body fields, with `task`/`units` always taking precedence: `$dhivehigpt->tasks()->calculate('audio_generation', 1000, body: [...])`.
