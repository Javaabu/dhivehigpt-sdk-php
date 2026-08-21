---
title: Laravel Usage
sidebar_position: 2
---

Once your API key is set in `config/services.php` (see [Installation & Setup](../installation-and-setup)), you can resolve the client three ways.

## Using the facade

```php
use Javaabu\DhivehiGpt\Facades\DhivehiGpt;

$voices = DhivehiGpt::voices()->list();
```

## Using dependency injection

```php
use Javaabu\DhivehiGpt\DhivehiGpt;

class TtsController
{
    public function store(DhivehiGpt $dhivehigpt)
    {
        return $dhivehigpt->tts()->generate('ދިވެހިޖީޕީޓީއަށް މަރުޙަބާ', 'hajja');
    }
}
```

## Resolving from the container

```php
$dhivehigpt = app('dhivehigpt');
// or
$dhivehigpt = app(\Javaabu\DhivehiGpt\DhivehiGpt::class);
```

In all three cases you get back the same singleton instance, configured from `config/services.php`.
