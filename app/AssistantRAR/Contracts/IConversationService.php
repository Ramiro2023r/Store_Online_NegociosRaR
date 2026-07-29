<?php

namespace App\AssistantRAR\Contracts;

interface IConversationService
{
    public function create(int $userId, ?string $title): array;
    public function list(int $userId): array;
    public function get(int $conversationId, int $userId): array;
    public function rename(int $conversationId, int $userId, string $title): array;
    public function delete(int $conversationId, int $userId): void;
    public function addMessage(int $conversationId, string $role, string $content, ?array $metadata = null): array;
    public function getHistory(int $conversationId, int $limit = 50): array;
}
