<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class SupportService
{
    public function conversations(?string $status = null): array
    {
        $q = Conversation::with('user:id,name', 'messages');
        if ($status) $q->where('status', $status);
        return $q->latest()->get()->toArray();
    }

    public function getConversation(int $id): ?Conversation
    {
        return Conversation::with('user:id,name', 'messages.user:id,name')->find($id);
    }

    public function reply(int $conversationId, string $body, int $staffId): Message
    {
        $conversation = Conversation::findOrFail($conversationId);
        if ($conversation->status === 'closed') {
            $conversation->update(['status' => 'open']);
        }
        return Message::create([
            'conversation_id' => $conversationId,
            'user_id' => $staffId,
            'is_staff' => true,
            'body' => $body,
        ]);
    }

    public function close(int $conversationId): void
    {
        Conversation::findOrFail($conversationId)->update(['status' => 'closed']);
    }

    public function reopen(int $conversationId): void
    {
        Conversation::findOrFail($conversationId)->update(['status' => 'open']);
    }
}
