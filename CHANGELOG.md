# Changelog

All notable changes will be documented in this file

## 1.0.0 - 2026-08-21

Initial release.

- PHP / Laravel SDK for the [DhivehiGPT API](https://dhivehigpt.com/docs), covering voices, text-to-speech, subscription balance, and tasks
- Framework-agnostic client (`Javaabu\DhivehiGpt\DhivehiGpt`), built on [Guzzle](https://docs.guzzlephp.org) — works in plain PHP as well as Laravel
- Laravel service provider and `DhivehiGpt` facade, configured via `config/services.php`
- `DhivehiGpt::fake()` for testing: records requests and returns queued responses without hitting the real API
- Supports PHP 8.0+ and Laravel 9 through 13
