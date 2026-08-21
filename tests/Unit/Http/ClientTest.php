<?php

namespace Javaabu\DhivehiGpt\Tests\Unit\Http;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response;
use Javaabu\DhivehiGpt\Tests\TestSupport\MocksGuzzle;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    use MocksGuzzle;

    public function test_get_builds_the_correct_url_with_query_parameters(): void
    {
        $client = $this->makeClient([new Response(200, [], '{"data":[]}')]);

        $client->get('voices', ['per_page' => 5, 'page' => 2]);

        $this->assertSame(
            'https://api.dhivehigpt.com/v1/voices?per_page=5&page=2',
            (string) $this->lastRequest()->getUri()
        );
    }

    public function test_get_omits_null_query_parameters(): void
    {
        $client = $this->makeClient([new Response(200, [], '{"data":[]}')]);

        $client->get('voices', ['search' => null, 'gender' => 'female']);

        $this->assertSame(
            'https://api.dhivehigpt.com/v1/voices?gender=female',
            (string) $this->lastRequest()->getUri()
        );
    }

    public function test_post_sends_a_json_encoded_body_with_the_content_type_header(): void
    {
        $client = $this->makeClient([new Response(201, [], '{"data":{}}')]);

        $client->post('tts', ['text' => 'hello', 'voice' => 'hajja']);

        $request = $this->lastRequest();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertSame('{"text":"hello","voice":"hajja"}', (string) $request->getBody());
    }

    public function test_put_sends_the_put_method(): void
    {
        $client = $this->makeClient([new Response(200, [], '{"data":{}}')]);

        $client->put('tts/abc', ['title' => 'New title']);

        $this->assertSame('PUT', $this->lastRequest()->getMethod());
    }

    public function test_delete_sends_the_delete_method_without_a_body(): void
    {
        $client = $this->makeClient([new Response(204, [], '')]);

        $result = $client->delete('tts/abc');

        $request = $this->lastRequest();

        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('', (string) $request->getBody());
        $this->assertSame([], $result);
    }

    public function test_it_returns_the_decoded_data_for_a_successful_response(): void
    {
        $client = $this->makeClient([new Response(200, [], '{"data":{"uuid":"abc"}}')]);

        $result = $client->get('tts/abc');

        $this->assertSame(['data' => ['uuid' => 'abc']], $result);
    }

    public function test_it_returns_an_empty_array_for_an_empty_body(): void
    {
        $client = $this->makeClient([new Response(204, [], '')]);

        $result = $client->get('voices');

        $this->assertSame([], $result);
    }

    public function test_it_forwards_a_custom_timeout_to_the_guzzle_client(): void
    {
        $client = $this->makeClient([new Response(200, [], '{"data":{}}')]);

        $client->post('tts', ['text' => 'hello', 'voice' => 'hajja'], 120.0);

        $this->assertSame(120.0, $this->lastRequestOptions()['timeout']);
    }

    public function test_it_does_not_catch_client_error_responses(): void
    {
        $client = $this->makeClient([new Response(404, [], '{"message":"Not found"}')]);

        $this->expectException(ClientException::class);

        $client->get('voices/unknown-voice');
    }

    public function test_it_does_not_catch_connection_failures(): void
    {
        $connect_exception = new ConnectException(
            'Could not resolve host',
            new GuzzleRequest('GET', 'https://api.dhivehigpt.com/v1/voices')
        );

        $client = $this->makeClient([$connect_exception]);

        $this->expectException(ConnectException::class);

        $client->get('voices');
    }
}
