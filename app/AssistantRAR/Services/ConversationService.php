<?php

namespace App\AssistantRAR\Services;

use App\AssistantRAR\Contracts\IConversationService;
use App\AssistantRAR\Models\AssistantConversation;
use App\AssistantRAR\Models\AssistantMessage;

class ConversationService implements IConversationService
{
    public function create(int $userId, ?string $title): array
    {
        $conversation = AssistantConversation::create([
            'user_id' => $userId,
            'title' => $title ?? 'Nueva conversación',
        ]);

        return $conversation->toArray();
    }

    public function list(int $userId): array
    {
        return AssistantConversation::where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->get()
            ->toArray();
    }

    public function get(int $conversationId, int $userId): array
    {
        $conversation = AssistantConversation::where('id', $conversationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $messages = AssistantMessage::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get()
            ->toArray();

        return [
            'conversation' => $conversation->toArray(),
            'messages' => $messages,
        ];
    }

    public function rename(int $conversationId, int $userId, string $title): array
    {
        $conversation = AssistantConversation::where('id', $conversationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $conversation->update(['title' => $title]);

        return $conversation->toArray();
    }

    public function delete(int $conversationId, int $userId): void
    {
        AssistantConversation::where('id', $conversationId)
            ->where('user_id', $userId)
            ->delete();
    }

    public function addMessage(int $conversationId, string $role, string $content, ?array $metadata = null): array
    {
        $message = AssistantMessage::create([
            'conversation_id' => $conversationId,
            'role' => $role,
            'content' => $content,
            'metadata' => $metadata,
        ]);

        $message->conversation->touch();

        return $message->toArray();
    }

    public function getHistory(int $conversationId, int $limit = 50): array
    {
        return AssistantMessage::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
