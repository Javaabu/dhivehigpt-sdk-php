<?php

namespace Javaabu\DhivehiGpt\Modules;

/**
 * Browse the available Text-to-Speech (TTS) voices available for audio generation.
 */
class VoicesModule extends Module
{
    /**
     * List the available voices.
     *
     * Supported $params keys: search, per_page, page, sort, is_premium, gender, age_group.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function list(array $params = [], ?float $timeout = null): array
    {
        return $this->client->get('voices', $params, $timeout);
    }

    /**
     * Get a single available voice identified by its slug.
     *
     * @return array<string, mixed>
     */
    public function get(string $slug, ?float $timeout = null): array
    {
        return $this->client->get("voices/{$slug}", [], $timeout);
    }
}
