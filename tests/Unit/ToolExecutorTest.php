<?php

namespace Tests\Unit;

use App\AssistantRAR\Contracts\IAssistantTool;
use App\AssistantRAR\Contracts\IToolRegistry;
use App\AssistantRAR\DTO\ToolResult;
use App\AssistantRAR\Models\AssistantConversation;
use App\AssistantRAR\Models\AssistantToolLog;
use App\AssistantRAR\Services\ToolExecutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolExecutorTest extends TestCase
{
    use RefreshDatabase;

    private ToolExecutor $executor;
    private User $user;
    private AssistantConversation $conversation;
    private array $baseContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->conversation = AssistantConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Test',
        ]);
        $this->baseContext = [
            'user' => ['id' => $this->user->id, 'role' => 'admin'],
            'conversation_id' => $this->conversation->id,
        ];
    }

    public function test_returns_not_found_for_unknown_tool(): void
    {
        $registry = $this->createMock(IToolRegistry::class);
        $registry->method('get')->willReturn(null);

        $executor = new ToolExecutor($registry);
        $result = $executor->execute('nonexistent.tool', [], $this->baseContext);

        $this->assertFalse($result['success']);
        $this->assertEquals('TOOL_NOT_FOUND', $result['errorCode']);
    }

    public function test_returns_disabled_for_disabled_tool(): void
    {
        $registry = $this->createMock(IToolRegistry::class);
        $registry->method('get')->willReturn([
            'name' => 'test.tool',
            'handler' => \App\AssistantRAR\Tools\BrandSearchTool::class,
            'roles' => ['admin'],
            'enabled' => false,
        ]);

        $executor = new ToolExecutor($registry);
        $result = $executor->execute('test.tool', [], $this->baseContext);

        $this->assertFalse($result['success']);
        $this->assertEquals('TOOL_DISABLED', $result['errorCode']);
    }

    public function test_returns_forbidden_for_wrong_role(): void
    {
        $registry = $this->createMock(IToolRegistry::class);
        $registry->method('get')->willReturn([
            'name' => 'admin.tool',
            'handler' => \App\AssistantRAR\Tools\UserSearchTool::class,
            'roles' => ['admin'],
            'enabled' => true,
        ]);

        $executor = new ToolExecutor($registry);
        $context = ['user' => ['id' => 2, 'role' => 'cliente']];
        $result = $executor->execute('admin.tool', [], $context);

        $this->assertFalse($result['success']);
        $this->assertEquals('FORBIDDEN', $result['errorCode']);
    }

    public function test_executes_tool_and_returns_result(): void
    {
        $handler = $this->createMock(IAssistantTool::class);
        $handler->method('name')->willReturn('test.tool');
        $handler->method('execute')->willReturn([
            'success' => true,
            'message' => 'Operación exitosa.',
            'data' => ['id' => 42],
            'errorCode' => null,
            'metadata' => ['resource_type' => 'test', 'resource_id' => 42],
        ]);

        $registry = $this->createMock(IToolRegistry::class);
        $registry->method('get')->willReturn([
            'name' => 'test.tool',
            'handler' => get_class($handler),
            'roles' => ['admin'],
            'enabled' => true,
        ]);

        $this->app->instance(get_class($handler), $handler);

        $executor = new ToolExecutor($registry);
        $result = $executor->execute('test.tool', ['foo' => 'bar'], $this->baseContext);

        $this->assertTrue($result['success']);
        $this->assertEquals('Operación exitosa.', $result['message']);
    }

    public function test_logs_tool_execution(): void
    {
        $handler = $this->createMock(IAssistantTool::class);
        $handler->method('execute')->willReturn(ToolResult::success('ok')->toArray());

        $registry = $this->createMock(IToolRegistry::class);
        $registry->method('get')->willReturn([
            'name' => 'test.tool',
            'handler' => get_class($handler),
            'roles' => ['admin'],
            'enabled' => true,
        ]);

        $this->app->instance(get_class($handler), $handler);

        $executor = new ToolExecutor($registry);
        $executor->execute('test.tool', ['x' => 1], $this->baseContext);

        $this->assertDatabaseHas('assistant_tool_logs', [
            'tool_name' => 'test.tool',
            'status' => 'completed',
        ]);
    }

    public function test_logs_failed_execution(): void
    {
        $handler = $this->createMock(IAssistantTool::class);
        $handler->method('execute')->willThrowException(new \RuntimeException('Error interno'));

        $registry = $this->createMock(IToolRegistry::class);
        $registry->method('get')->willReturn([
            'name' => 'bad.tool',
            'handler' => get_class($handler),
            'roles' => ['admin'],
            'enabled' => true,
        ]);

        $this->app->instance(get_class($handler), $handler);

        $executor = new ToolExecutor($registry);
        $executor->execute('bad.tool', ['invalid' => true], $this->baseContext);

        $this->assertDatabaseHas('assistant_tool_logs', [
            'tool_name' => 'bad.tool',
            'status' => 'failed',
        ]);
    }
}
