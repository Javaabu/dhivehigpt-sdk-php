<?php

namespace Javaabu\DhivehiGpt\Tests\Unit\Modules;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Javaabu\DhivehiGpt\Modules\TasksModule;
use Javaabu\DhivehiGpt\Tests\TestSupport\MocksGuzzle;
use PHPUnit\Framework\TestCase;

class TasksModuleTest extends TestCase
{
    use MocksGuzzle;

    public function test_list_requests_the_tasks_endpoint(): void
    {
        $module = new TasksModule($this->makeClient([new Response(200, [], '{"data":[]}')]));

        $module->list(['platform' => 'tts']);

        $this->assertSame('https://api.dhivehigpt.com/v1/tasks?platform=tts', (string) $this->lastRequest()->getUri());
    }

    public function test_list_forwards_a_custom_timeout(): void
    {
        $module = new TasksModule($this->makeClient([new Response(200, [], '{"data":[]}')]));

        $module->list([], 5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_get_requests_a_single_task_by_slug(): void
    {
        $module = new TasksModule($this->makeClient([new Response(200, [], '{"data":{"slug":"audio_generation"}}')]));

        $module->get('audio_generation');

        $this->assertSame('https://api.dhivehigpt.com/v1/tasks/audio_generation', (string) $this->lastRequest()->getUri());
    }

    public function test_get_forwards_a_custom_timeout(): void
    {
        $module = new TasksModule($this->makeClient([new Response(200, [], '{"data":{}}')]));

        $module->get('audio_generation', 5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_calculate_posts_the_task_and_units(): void
    {
        $module = new TasksModule($this->makeClient([new Response(200, [], '{"charged_credits":10,"can_proceed":true}')]));

        $result = $module->calculate('audio_generation', 1000);

        $request = $this->lastRequest();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://api.dhivehigpt.com/v1/tasks/calculate', (string) $request->getUri());
        $this->assertSame(json_encode(['task' => 'audio_generation', 'units' => 1000]), (string) $request->getBody());
        $this->assertSame(['charged_credits' => 10, 'can_proceed' => true], $result);
    }

    public function test_calculate_forwards_a_custom_timeout(): void
    {
        $module = new TasksModule($this->makeClient([new Response(200, [], '{"can_proceed":true}')]));

        $module->calculate('audio_generation', 1000, timeout: 5.0);

        $this->assertSame(5.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_calculate_merges_extra_body_fields(): void
    {
        $module = new TasksModule($this->makeClient([new Response(200, [], '{"can_proceed":true}')]));

        $module->calculate('audio_generation', 1000, ['platform' => 'tts']);

        $this->assertSame(
            json_encode(['platform' => 'tts', 'task' => 'audio_generation', 'units' => 1000]),
            (string) $this->lastRequest()->getBody()
        );
    }

    public function test_calculate_lets_guzzle_throw_for_an_invalid_task(): void
    {
        $module = new TasksModule($this->makeClient([
            new Response(422, [], '{"message":"The selected task is invalid.","errors":{"task":["The selected task is invalid."]}}'),
        ]));

        $this->expectException(ClientException::class);

        $module->calculate('not-a-real-task', 1000);
    }
}
