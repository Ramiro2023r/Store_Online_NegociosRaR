<?php

namespace Tests\Unit;

use App\AssistantRAR\Services\ProviderManager;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProviderManagerTest extends TestCase
{
    private ProviderManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        config(['assistant.api_key' => 'test-key-12345']);
        config(['assistant.provider' => 'groq']);
        config(['assistant.model' => 'llama-3.3-70b-versatile']);
        $this->manager = new ProviderManager();
    }

    public function test_returns_not_configured_when_api_key_empty(): void
    {
        config(['assistant.api_key' => '']);
        $manager = new ProviderManager();

        $result = $manager->sendMessage([['role' => 'user', 'content' => 'hola']], [], []);

        $this->assertEquals('El asistente no está configurado. Contacta al administrador.', $result['content']);
        $this->assertEmpty($result['tool_calls']);
    }

    public function test_returns_error_on_http_failure(): void
    {
        Http::fake(['api.groq.com/*' => Http::response(null, 500)]);

        $result = $this->manager->sendMessage(
            [['role' => 'user', 'content' => 'hola']], [], []
        );

        $this->assertEquals('Error al conectar con el asistente. Intenta nuevamente.', $result['content']);
    }

    public function test_returns_content_on_success(): void
    {
        Http::fake(['api.groq.com/*' => Http::response([
            'choices' => [['message' => ['content' => '¡Hola! ¿En qué puedo ayudarte?']]],
        ], 200)]);

        $result = $this->manager->sendMessage(
            [['role' => 'user', 'content' => 'hola']], [], []
        );

        $this->assertEquals('¡Hola! ¿En qué puedo ayudarte?', $result['content']);
        $this->assertEmpty($result['tool_calls']);
    }

    public function test_parses_tool_calls_from_response(): void
    {
        Http::fake(['api.groq.com/*' => Http::response([
            'choices' => [[
                'message' => [
                    'content' => null,
                    'tool_calls' => [
                        [
                            'id' => 'call_abc123',
                            'type' => 'function',
                            'function' => [
                                'name' => 'product.search',
                                'arguments' => '{"query":"zapatos"}',
                            ],
                        ],
                    ],
                ],
            ]],
        ], 200)]);

        $result = $this->manager->sendMessage(
            [['role' => 'user', 'content' => 'busca zapatos']],
            [['name' => 'product.search', 'description' => 'Buscar productos', 'input_schema' => ['type' => 'object', 'properties' => new \stdClass()]]],
            []
        );

        $this->assertEmpty($result['content']);
        $this->assertCount(1, $result['tool_calls']);
        $this->assertEquals('product.search', $result['tool_calls'][0]['name']);
        $this->assertEquals(['query' => 'zapatos'], $result['tool_calls'][0]['arguments']);
    }

    public function test_stream_returns_not_configured_when_api_key_empty(): void
    {
        config(['assistant.api_key' => '']);
        $manager = new ProviderManager();
        $output = '';

        $manager->sendMessageStream(
            [['role' => 'user', 'content' => 'hola']], [], [],
            function ($chunk) use (&$output) { $output .= $chunk; }
        );

        $this->assertStringContainsString('no está configurado', $output);
    }

    public function test_get_provider_name(): void
    {
        $this->assertEquals('groq', $this->manager->getProviderName());
    }

    public function test_formats_tools_with_empty_properties_as_object(): void
    {
        $tools = [
            ['name' => 'test.tool', 'description' => 'Test', 'input_schema' => ['type' => 'object', 'properties' => []]],
        ];

        $ref = new \ReflectionMethod($this->manager, 'formatToolsForGroq');
        $ref->setAccessible(true);
        $result = $ref->invoke($this->manager, $tools);

        $encoded = json_encode($result);
        $this->assertStringContainsString('"properties":{}', $encoded);
        $this->assertStringNotContainsString('"properties":[]', $encoded);
    }
}
