---
title: Error Handling
sidebar_position: 7
---

The SDK does not wrap errors in its own exception classes — it lets [Guzzle](https://docs.guzzlephp.org) raise its own exceptions, so you catch the same exceptions you would from any other Guzzle-based code:

```php
use GuzzleHttp\Exception\GuzzleException;

try {
    $audio = $dhivehigpt->tts()->generate($text, $voice);
} catch (GuzzleException $exception) {
    report($exception);
}
```

## Exception types

| Exception | Thrown when |
|---|---|
| `GuzzleHttp\Exception\ClientException` | The API responded with a 4xx status — e.g. a missing/invalid/revoked API key (401), insufficient credits (403), not found (404), a validation error (422), or the rate limit of 60 requests per minute was exceeded (429). |
| `GuzzleHttp\Exception\ServerException` | The API responded with a 5xx status. |
| `GuzzleHttp\Exception\ConnectException` | The request could not connect at all — a DNS failure, connection timeout, etc. |
| `GuzzleHttp\Exception\RequestException` | The base class for the exceptions above — the common type to catch if you don't need to distinguish between them. It also exposes `getResponse()`. |
| `GuzzleHttp\Exception\GuzzleException` | The interface implemented by every exception above — catch this if you just want to handle "the request failed" in general. |

## Reading the response body

`ClientException` and `ServerException` both expose the failed response via `getResponse()`. The DhivehiGPT API always returns a JSON body with a `message` key, and a `errors` key for validation failures (HTTP 422):

```php
use GuzzleHttp\Exception\ClientException;

try {
    $dhivehigpt->tasks()->calculate('not-a-real-task', 1000);
} catch (ClientException $exception) {
    $data = json_decode((string) $exception->getResponse()->getBody(), true);

    $message = $data['message']; // 'The selected task is invalid.'
    $errors = $data['errors'] ?? []; // ['task' => ['The selected task is invalid.']]
}
```
