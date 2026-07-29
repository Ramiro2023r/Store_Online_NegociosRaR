<?php

namespace Tests\Unit;

use App\AssistantRAR\Contracts\IContextService;
use App\AssistantRAR\Contracts\IConversationService;
use App\AssistantRAR\Contracts\IPromptBuilder;
use App\AssistantRAR\Contracts\IProviderManager;
use App\AssistantRAR\Contracts\IToolExecutor;
use App\AssistantRAR\Contracts\IToolRegistry;
use App\AssistantRAR\Services\AssistantService;
use Tests\TestCase;

class AssistantServiceTest extends TestCase
{
    private AssistantService $service;
    private IConversationService $conversation;
    private IContextService $context;
    private IPromptBuilder $prompt;
    private IProviderManager $provider;
    private IToolExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conversation = $this->createMock(IConversationService::class);
        $this->context = $this->createMock(IContextService::class);
        $this->prompt = $this->createMock(IPromptBuilder::class);
        $this->provider = $this->createMock(IProviderManager::class);
        $this->executor = $this->createMock(IToolExecutor::class);
        $registry = $this->createMock(IToolRegistry::class);

        $this->context->method('build')->willReturn([
            'user' => ['id' => 1, 'role' => 'admin'],
            'available_tools' => [],
            'history' => [],
            'company' => ['name' => 'T', 'currency_symbol' => 'S/', 'currency' => 'PEN'],
            'locale' => 'es',
            'timezone' => 'UTC',
        ]);

        $this->prompt->method('buildSystemPrompt')->willReturn('System prompt');
        $this->prompt->method('buildUserPrompt')->willReturnArgument(0);

        $this->service = new AssistantService(
            $this->conversation,
            $this->context,
            $this->prompt,
            $this->provider,
            $registry,
            $this->executor,
        );
    }

    public function test_process_message_stores_user_message(): void
    {
        $this->provider->method('sendMessage')->willReturn([
            'content' => '¡Hola! ¿En qué puedo ayudarte?',
            'tool_calls' => [],
        ]);

        $called = [];
        $this->conversation->method('addMessage')
            ->willReturnCallback(function (...$args) use (&$called) {
                $called[] = $args;
                return ['id' => count($called)];
            });

        $this->service->processMessage(1, 1, 'hola');

        $this->assertCount(2, $called);
        $this->assertEquals([1, 'user', 'hola'], array_slice($called[0], 0, 3));
        $this->assertEquals([1, 'assistant', '¡Hola! ¿En qué puedo ayudarte?'], array_slice($called[1], 0, 3));
    }

    public function test_process_message_returns_content(): void
    {
        $this->provider->method('sendMessage')->willReturn([
            'content' => 'Respuesta de prueba',
            'tool_calls' => [],
        ]);

        $result = $this->service->processMessage(1, 1, 'prueba');

        $this->assertEquals('Respuesta de prueba', $result['content']);
    }

    public function test_process_message_executes_tool_loop(): void
    {
        $this->provider->method('sendMessage')->willReturnOnConsecutiveCalls(
            ['content' => null, 'tool_calls' => [
                ['id' => 'call_1', 'name' => 'test.tool', 'arguments' => ['x' => 1]],
            ]],
            ['content' => 'Tool ejecutado correctamente.', 'tool_calls' => []],
        );

        $this->executor->method('execute')->willReturn([
            'success' => true, 'message' => 'Tool result', 'data' => [],
        ]);

        $called = [];
        $this->conversation->method('addMessage')
            ->willReturnCallback(function (...$args) use (&$called) {
                $called[] = $args;
                return ['id' => count($called)];
            });

        $result = $this->service->processMessage(1, 1, 'ejecuta tool');

        $this->assertEquals('Tool ejecutado correctamente.', $result['content']);
        $this->assertEquals([1, 'user', 'ejecuta tool'], array_slice($called[0], 0, 3));
        $this->assertEquals([1, 'assistant', 'Tool ejecutado correctamente.'], array_slice($called[1], 0, 3));
    }

    public function test_process_message_handles_multiple_tools(): void
    {
        $this->provider->method('sendMessage')->willReturnOnConsecutiveCalls(
            ['content' => null, 'tool_calls' => [
                ['id' => 'call_1', 'name' => 'tool.one', 'arguments' => []],
                ['id' => 'call_2', 'name' => 'tool.two', 'arguments' => []],
            ]],
            ['content' => 'Ambos tools ejecutados.', 'tool_calls' => []],
        );

        $this->executor->method('execute')->willReturn([
            'success' => true, 'message' => 'ok', 'data' => [],
        ]);

        $this->executor->expects($this->exactly(2))->method('execute');

        $result = $this->service->processMessage(1, 1, 'ejecuta dos');

        $this->assertEquals('Ambos tools ejecutados.', $result['content']);
    }

    public function test_process_message_stops_after_max_iterations(): void
    {
        $this->provider->method('sendMessage')->willReturn([
            'content' => null,
            'tool_calls' => [['id' => 'call_1', 'name' => 'loop.tool', 'arguments' => []]],
        ]);

        $this->executor->method('execute')->willReturn([
            'success' => true, 'message' => 'loop', 'data' => [],
        ]);

        $result = $this->service->processMessage(1, 1, 'bucle infinito');

        $this->assertNotEmpty($result['content']);
    }

    public function test_process_message_uses_tools_only_on_first_iteration(): void
    {
        $this->context->method('build')->willReturn([
            'user' => ['id' => 1, 'role' => 'admin'],
            'available_tools' => ['tool.one' => ['name' => 'tool.one']],
            'history' => [],
            'company' => ['name' => 'T', 'currency_symbol' => 'S/', 'currency' => 'PEN'],
            'locale' => 'es',
            'timezone' => 'UTC',
        ]);

        $this->provider->method('sendMessage')
            ->willReturnOnConsecutiveCalls(
                ['content' => null, 'tool_calls' => [
                    ['id' => 'call_1', 'name' => 'tool.one', 'arguments' => []],
                ]],
                ['content' => 'Finalizado.', 'tool_calls' => []],
            );

        $this->executor->method('execute')->willReturn([
            'success' => true, 'message' => 'ok', 'data' => [],
        ]);

        $this->provider->expects($this->exactly(2))
            ->method('sendMessage')
            ->willReturnOnConsecutiveCalls(
                ['content' => null, 'tool_calls' => [
                    ['id' => 'call_1', 'name' => 'tool.one', 'arguments' => []],
                ]],
                ['content' => 'Finalizado.', 'tool_calls' => []],
            );

        $result = $this->service->processMessage(1, 1, 'test tools only first');

        $this->assertEquals('Finalizado.', $result['content']);
    }
}
