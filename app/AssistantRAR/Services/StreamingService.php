<?php

namespace App\AssistantRAR\Services;

use App\AssistantRAR\Contracts\IStreamingService;
use App\AssistantRAR\Contracts\IAssistantService;
use App\AssistantRAR\Contracts\IConversationService;

class StreamingService implements IStreamingService
{
    public function __construct(
        private readonly IAssistantService $assistant,
        private readonly IConversationService $conversation,
    ) {}

    public function streamResponse(int $conversationId, string $message, callable $onChunk): void
    {
        $this->assistant->processStream(
            auth()->id(),
            $conversationId,
            $message,
            $onChunk,
        );
    }

    public function sendSSE(int $userId, string $event, string $data): void
    {
        // TODO: implementar SSE canal por usuario
    }
}
