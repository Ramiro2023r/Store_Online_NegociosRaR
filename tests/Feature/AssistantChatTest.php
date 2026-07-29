<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistantChatTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'active' => true]);
    }

    public function test_can_list_conversations(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/asistente/conversaciones');
        $response->assertOk()->assertJson([]);
    }

    public function test_can_create_conversation(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/asistente/conversaciones', [
            'title' => 'Test',
        ]);
        $response->assertCreated()->assertJsonFragment(['title' => 'Test']);
    }

    public function test_send_message_requires_auth(): void
    {
        $response = $this->postJson('/asistente/conversaciones/1/mensaje', [
            'message' => 'hola',
        ]);
        $response->assertStatus(302);
    }

    public function test_can_get_memories(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/asistente/memorias');
        $response->assertOk()->assertJson([]);
    }
}
