<?php

namespace Tests\Unit;

use App\AssistantRAR\Contracts\IConversationService;
use App\AssistantRAR\Contracts\IMemoryService;
use App\AssistantRAR\Contracts\IToolRegistry;
use App\AssistantRAR\Services\ContextService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContextServiceTest extends TestCase
{
    use RefreshDatabase;

    private function mockRegistryWithTools(array $toolNames): IToolRegistry
    {
        $tools = array_map(fn ($name) => [
            'name' => $name,
            'description' => $name,
            'input_schema' => ['type' => 'object', 'properties' => []],
            'roles' => ['admin', 'trabajador', 'cliente'],
            'confirmation_level' => 0,
            'enabled' => true,
        ], $toolNames);

        $registry = $this->createMock(IToolRegistry::class);
        $registry->method('getForRole')->willReturn($tools);
        return $registry;
    }

    public function test_build_includes_user_info(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'active' => true]);

        $memory = $this->createMock(IMemoryService::class);
        $memory->method('getAll')->willReturn([]);

        $conversation = $this->createMock(IConversationService::class);

        $service = new ContextService($memory, $conversation, $this->mockRegistryWithTools(['product.search']));
        $context = $service->build($user->id);

        $this->assertEquals($user->id, $context['user']['id']);
        $this->assertEquals($user->role, $context['user']['role']);
        $this->assertTrue($context['user']['is_staff']);
        $this->assertTrue($context['user']['is_admin']);
        $this->assertArrayHasKey('permissions', $context);
        $this->assertArrayHasKey('company', $context);
        $this->assertArrayHasKey('available_tools', $context);
    }

    public function test_getAvailableTools_delegates_to_registry(): void
    {
        $user = User::factory()->create(['role' => 'cliente', 'active' => true]);

        $memory = $this->createMock(IMemoryService::class);
        $memory->method('getAll')->willReturn([]);

        $conversation = $this->createMock(IConversationService::class);

        $registry = $this->mockRegistryWithTools(['product.search', 'cart.get']);
        $registry->expects($this->once())
            ->method('getForRole')
            ->with('cliente');

        $service = new ContextService($memory, $conversation, $registry);
        $service->getAvailableTools($user->id);
    }

    public function test_client_tools_do_not_include_admin_tools(): void
    {
        $user = User::factory()->create(['role' => 'cliente', 'active' => true]);

        $memory = $this->createMock(IMemoryService::class);
        $memory->method('getAll')->willReturn([]);

        $conversation = $this->createMock(IConversationService::class);

        $registry = $this->mockRegistryWithTools(['product.search', 'cart.get']);

        $service = new ContextService($memory, $conversation, $registry);
        $tools = $service->getAvailableTools($user->id);

        $toolNames = array_column($tools, 'name');
        $this->assertContains('product.search', $toolNames);
        $this->assertContains('cart.get', $toolNames);
    }

    public function test_admin_tools_include_all(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'active' => true]);

        $memory = $this->createMock(IMemoryService::class);
        $memory->method('getAll')->willReturn([]);

        $conversation = $this->createMock(IConversationService::class);

        $registry = $this->mockRegistryWithTools(['product.search', 'product.create', 'user.search']);

        $service = new ContextService($memory, $conversation, $registry);
        $tools = $service->getAvailableTools($user->id);

        $toolNames = array_column($tools, 'name');
        $this->assertContains('product.search', $toolNames);
        $this->assertContains('product.create', $toolNames);
        $this->assertContains('user.search', $toolNames);
    }
}
