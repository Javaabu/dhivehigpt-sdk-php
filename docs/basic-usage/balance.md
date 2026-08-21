---
title: Subscription Balance
sidebar_position: 5
---

View the subscription and credit balance associated with the current API key's team, via `$dhivehigpt->balance()`.

```php
$balance = $dhivehigpt->balance()->get();

echo $balance['billing_period']['balance']; // remaining credits in the current billing period
```

The `subscription` and `billing_period` keys are `null` when the team has no active subscription.
