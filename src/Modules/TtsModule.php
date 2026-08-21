<?php

namespace Javaabu\DhivehiGpt\Modules;

/**
 * Generate and manage API-created Text-to-Speech (TTS) audios.
 */
class TtsModule extends Module
{
    /**
     * Audio generation can take a while for longer text, so requests to
     * generate a TTS audio are given a generous default timeout, unless
     * overridden via the $timeout argument.
     */
    public const GENERATION_TIMEOUT = 120.0;

    /**
     * List the TTS audios created by the current API key's team.
     *
     * Supported $params keys: search, per_page, page, sort, voice, date_from, date_to.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function list(array $params = [], ?float $timeout = null): array
    {
        return $this->client->get('tts', $params, $timeout);
    }

    /**
     * Convert text into speech with an available voice.
     *
     * $body can be used to pass additional request body fields not yet covered by
     * this method's arguments; text/voice always take precedence over $body.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function generate(string $text, string $voice, array $body = [], ?float $timeout = null): array
    {
        return $this->client->post('tts', array_merge($body, [
            'text' => $text,
            'voice' => $voice,
        ]), $timeout ?? self::GENERATION_TIMEOUT);
    }

    /**
     * Get a single TTS audio belonging to the current API key's team.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid, ?float $timeout = null): array
    {
        return $this->client->get("tts/{$uuid}", [], $timeout);
    }

    /**
     * Update the title of a TTS audio belonging to the current API key's team.
     *
     * $body can be used to pass additional request body fields not yet covered by
     * this method's arguments; title always takes precedence over $body.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function update(string $uuid, string $title, array $body = [], ?float $timeout = null): array
    {
        return $this->client->put("tts/{$uuid}", array_merge($body, [
            'title' => $title,
        ]), $timeout);
    }

    /**
     * Delete a TTS audio belonging to the current API key's team.
     */
    public function delete(string $uuid, ?float $timeout = null): void
    {
        $this->client->delete("tts/{$uuid}", $timeout);
    }
}
