<?php

namespace Javaabu\DhivehiGpt\Tests\Unit\Modules;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Javaabu\DhivehiGpt\Modules\TtsModule;
use Javaabu\DhivehiGpt\Tests\TestSupport\MocksGuzzle;
use PHPUnit\Framework\TestCase;

class TtsModuleTest extends TestCase
{
    use MocksGuzzle;

    public function test_list_requests_the_tts_endpoint(): void
    {
        $module = new TtsModule($this->makeClient([new Response(200, [], '{"data":[]}')]));

        $module->list(['voice' => 'hajja']);

        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('https://api.dhivehigpt.com/v1/tts?voice=hajja', (string) $this->lastRequest()->getUri());
    }

    public function test_list_forwards_a_custom_timeout(): void
    {
        $module = new TtsModule($this->makeClient([new Response(200, [], '{"data":[]}')]));

        $module->list([], 5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_generate_posts_the_text_and_voice(): void
    {
        $module = new TtsModule($this->makeClient([new Response(201, [], '{"data":{"uuid":"abc"}}')]));

        $result = $module->generate('ދިވެހިޖީޕީޓީއަށް މަރުޙަބާ', 'hajja');

        $request = $this->lastRequest();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://api.dhivehigpt.com/v1/tts', (string) $request->getUri());
        $this->assertSame(
            json_encode(['text' => 'ދިވެހިޖީޕީޓީއަށް މަރުޙަބާ', 'voice' => 'hajja']),
            (string) $request->getBody()
        );
        $this->assertSame(['data' => ['uuid' => 'abc']], $result);
    }

    public function test_generate_defaults_to_the_generation_timeout(): void
    {
        $module = new TtsModule($this->makeClient([new Response(201, [], '{"data":{"uuid":"abc"}}')]));

        $module->generate('hello', 'hajja');

        $this->assertSame(TtsModule::GENERATION_TIMEOUT, $this->lastRequestOptions()['timeout']);
        $this->assertSame(120.0, TtsModule::GENERATION_TIMEOUT);
    }

    public function test_generate_can_override_the_generation_timeout(): void
    {
        $module = new TtsModule($this->makeClient([new Response(201, [], '{"data":{"uuid":"abc"}}')]));

        $module->generate('hello', 'hajja', timeout: 200.0);

        $this->assertSame(200.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_generate_merges_extra_body_fields(): void
    {
        $module = new TtsModule($this->makeClient([new Response(201, [], '{"data":{"uuid":"abc"}}')]));

        $module->generate('hello', 'hajja', ['speed' => 1.2]);

        $this->assertSame(
            json_encode(['speed' => 1.2, 'text' => 'hello', 'voice' => 'hajja']),
            (string) $this->lastRequest()->getBody()
        );
    }

    public function test_generate_lets_text_and_voice_take_precedence_over_body(): void
    {
        $module = new TtsModule($this->makeClient([new Response(201, [], '{"data":{"uuid":"abc"}}')]));

        $module->generate('hello', 'hajja', ['text' => 'ignored', 'voice' => 'ignored']);

        $this->assertSame(
            json_encode(['text' => 'hello', 'voice' => 'hajja']),
            (string) $this->lastRequest()->getBody()
        );
    }

    public function test_generate_lets_guzzle_throw_when_the_team_has_no_credits(): void
    {
        $module = new TtsModule($this->makeClient([new Response(403, [], '{"message":"You don\'t have any credits available."}')]));

        $this->expectException(ClientException::class);

        $module->generate('hello', 'hajja');
    }

    public function test_get_requests_a_single_tts_audio_by_uuid(): void
    {
        $module = new TtsModule($this->makeClient([new Response(200, [], '{"data":{"uuid":"abc"}}')]));

        $module->get('abc');

        $this->assertSame('https://api.dhivehigpt.com/v1/tts/abc', (string) $this->lastRequest()->getUri());
    }

    public function test_get_forwards_a_custom_timeout(): void
    {
        $module = new TtsModule($this->makeClient([new Response(200, [], '{"data":{}}')]));

        $module->get('abc', 5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_update_puts_the_new_title(): void
    {
        $module = new TtsModule($this->makeClient([new Response(200, [], '{"data":{"uuid":"abc","title":"New title"}}')]));

        $result = $module->update('abc', 'New title');

        $request = $this->lastRequest();

        $this->assertSame('PUT', $request->getMethod());
        $this->assertSame('https://api.dhivehigpt.com/v1/tts/abc', (string) $request->getUri());
        $this->assertSame(json_encode(['title' => 'New title']), (string) $request->getBody());
        $this->assertSame(['data' => ['uuid' => 'abc', 'title' => 'New title']], $result);
    }

    public function test_update_forwards_a_custom_timeout(): void
    {
        $module = new TtsModule($this->makeClient([new Response(200, [], '{"data":{}}')]));

        $module->update('abc', 'New title', timeout: 5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_update_merges_extra_body_fields(): void
    {
        $module = new TtsModule($this->makeClient([new Response(200, [], '{"data":{}}')]));

        $module->update('abc', 'New title', ['voice' => 'hajja']);

        $this->assertSame(
            json_encode(['voice' => 'hajja', 'title' => 'New title']),
            (string) $this->lastRequest()->getBody()
        );
    }

    public function test_delete_sends_a_delete_request(): void
    {
        $module = new TtsModule($this->makeClient([new Response(204, [], '')]));

        $module->delete('abc');

        $request = $this->lastRequest();

        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('https://api.dhivehigpt.com/v1/tts/abc', (string) $request->getUri());
    }

    public function test_delete_forwards_a_custom_timeout(): void
    {
        $module = new TtsModule($this->makeClient([new Response(204, [], '')]));

        $module->delete('abc', 5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }
}
