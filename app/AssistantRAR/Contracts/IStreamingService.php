<?php

namespace App\AssistantRAR\Contracts;

interface IStreamingService
{
    public function streamResponse(int $conversationId, string $message, callable $onChunk): void;
    public function sendSSE(int $userId, string $event, string $data): void;
}
