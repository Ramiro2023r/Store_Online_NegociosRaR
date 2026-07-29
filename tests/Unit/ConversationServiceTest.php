<?php

namespace Tests\Unit;

use App\AssistantRAR\Models\AssistantConversation;
use App\AssistantRAR\Models\AssistantMessage;
use App\AssistantRAR\Services\ConversationService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ConversationService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ConversationService();
        $this->user = User::factory()->create();
    }

    public function test_creates_conversation(): void
    {
        $conv = $this->service->create($this->user->id, 'Mi primera conversación');

        $this->assertDatabaseHas('assistant_conversations', [
            'id' => $conv['id'],
            'user_id' => $this->user->id,
            'title' => 'Mi primera conversación',
        ]);
    }

    public function test_creates_conversation_with_default_title(): void
    {
        $conv = $this->service->create($this->user->id, null);

        $this->assertEquals('Nueva conversación', $conv['title']);
    }

    public function test_lists_user_conversations(): void
    {
        $this->service->create($this->user->id, 'Conv 1');
        $this->service->create($this->user->id, 'Conv 2');

        $list = $this->service->list($this->user->id);

        $this->assertCount(2, $list);
    }

    public function test_list_excludes_other_users(): void
    {
        $otherUser = User::factory()->create();
        $this->service->create($this->user->id, 'Mía');
        $this->service->create($otherUser->id, 'De otro');

        $list = $this->service->list($this->user->id);

        $this->assertCount(1, $list);
        $this->assertEquals('Mía', $list[0]['title']);
    }

    public function test_gets_conversation_with_messages(): void
    {
        $conv = $this->service->create($this->user->id, 'Test');
        $this->service->addMessage($conv['id'], 'user', 'hola');
        $this->service->addMessage($conv['id'], 'assistant', '¿en qué puedo ayudarte?');

        $data = $this->service->get($conv['id'], $this->user->id);

        $this->assertArrayHasKey('conversation', $data);
        $this->assertArrayHasKey('messages', $data);
        $this->assertCount(2, $data['messages']);
    }

    public function test_get_fails_for_wrong_user(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $conv = $this->service->create($this->user->id, 'Privada');
        $otherUser = User::factory()->create();

        $this->service->get($conv['id'], $otherUser->id);
    }

    public function test_renames_conversation(): void
    {
        $conv = $this->service->create($this->user->id, 'Viejo título');
        $updated = $this->service->rename($conv['id'], $this->user->id, 'Nuevo título');

        $this->assertEquals('Nuevo título', $updated['title']);
    }

    public function test_deletes_conversation(): void
    {
        $conv = $this->service->create($this->user->id, 'A borrar');

        $this->service->delete($conv['id'], $this->user->id);

        $this->assertDatabaseMissing('assistant_conversations', ['id' => $conv['id']]);
    }

    public function test_adds_message(): void
    {
        $conv = $this->service->create($this->user->id, 'Test');

        $msg = $this->service->addMessage($conv['id'], 'user', 'contenido del mensaje', ['extra' => 'data']);

        $this->assertDatabaseHas('assistant_messages', [
            'id' => $msg['id'],
            'conversation_id' => $conv['id'],
            'role' => 'user',
            'content' => 'contenido del mensaje',
        ]);
    }

    public function test_add_message_updates_conversation_timestamp(): void
    {
        $conv = $this->service->create($this->user->id, 'Test');
        $originalUpdatedAt = AssistantConversation::find($conv['id'])->updated_at;

        $this->travel(1)->minute();
        $this->service->addMessage($conv['id'], 'user', 'nuevo mensaje');

        $this->assertNotEquals(
            $originalUpdatedAt->timestamp,
            AssistantConversation::find($conv['id'])->updated_at->timestamp
        );
    }

    public function test_gets_history_with_limit(): void
    {
        $conv = $this->service->create($this->user->id, 'Test');
        for ($i = 0; $i < 5; $i++) {
            $this->service->addMessage($conv['id'], 'user', "msg {$i}");
        }

        $history = $this->service->getHistory($conv['id'], 3);

        $this->assertCount(3, $history);
    }
}
