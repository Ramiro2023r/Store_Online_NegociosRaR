<?php

namespace App\AssistantRAR\Contracts;

interface IAssistantService
{
    public function processMessage(int $userId, int $conversationId, string $message): array;
    public function processStream(int $userId, int $conversationId, string $message, callable $onChunk): void;
}
