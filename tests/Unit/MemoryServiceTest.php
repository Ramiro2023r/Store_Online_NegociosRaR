<?php

namespace Tests\Unit;

use App\AssistantRAR\Services\MemoryService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private MemoryService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemoryService();
        $this->user = User::factory()->create();
    }

    public function test_sets_and_gets_memory(): void
    {
        $this->service->set($this->user->id, 'color_favorito', 'azul');

        $value = $this->service->get($this->user->id, 'color_favorito');
        $this->assertEquals('azul', $value);
    }

    public function test_set_updates_existing_memory(): void
    {
        $this->service->set($this->user->id, 'talla', '42');
        $this->service->set($this->user->id, 'talla', '44');

        $value = $this->service->get($this->user->id, 'talla');
        $this->assertEquals('44', $value);
    }

    public function test_get_returns_null_for_nonexistent_key(): void
    {
        $value = $this->service->get($this->user->id, 'no_existe');
        $this->assertNull($value);
    }

    public function test_set_with_category(): void
    {
        $this->service->set($this->user->id, 'peso', '70kg', 'salud');

        $all = $this->service->getAll($this->user->id);
        $this->assertCount(1, $all);
        $this->assertEquals('salud', $all[0]['category']);
    }

    public function test_deletes_memory(): void
    {
        $this->service->set($this->user->id, 'temp', 'valor');
        $this->service->delete($this->user->id, 'temp');

        $value = $this->service->get($this->user->id, 'temp');
        $this->assertNull($value);
    }

    public function test_get_all_returns_all_memories(): void
    {
        $this->service->set($this->user->id, 'key1', 'val1');
        $this->service->set($this->user->id, 'key2', 'val2');

        $all = $this->service->getAll($this->user->id);
        $this->assertCount(2, $all);
    }

    public function test_get_all_excludes_other_users(): void
    {
        $otherUser = User::factory()->create();
        $this->service->set($this->user->id, 'mia', 'dato');
        $this->service->set($otherUser->id, 'otro', 'privado');

        $all = $this->service->getAll($this->user->id);
        $this->assertCount(1, $all);
        $this->assertEquals('mia', $all[0]['key']);
    }

    public function test_get_by_category(): void
    {
        $this->service->set($this->user->id, 'altura', '180cm', 'fisico');
        $this->service->set($this->user->id, 'peso', '75kg', 'fisico');
        $this->service->set($this->user->id, 'email_contacto', 'x@y.com', 'contacto');

        $fisico = $this->service->getByCategory($this->user->id, 'fisico');
        $this->assertCount(2, $fisico);

        $contacto = $this->service->getByCategory($this->user->id, 'contacto');
        $this->assertCount(1, $contacto);
    }

    public function test_get_by_category_returns_empty_for_nonexistent(): void
    {
        $result = $this->service->getByCategory($this->user->id, 'no_existe');
        $this->assertEmpty($result);
    }
}
