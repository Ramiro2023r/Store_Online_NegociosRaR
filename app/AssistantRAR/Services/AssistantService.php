<?php

namespace App\AssistantRAR\Services;

use App\AssistantRAR\Contracts\IAssistantService;
use App\AssistantRAR\Contracts\IConversationService;
use App\AssistantRAR\Contracts\IContextService;
use App\AssistantRAR\Contracts\IPromptBuilder;
use App\AssistantRAR\Contracts\IProviderManager;
use App\AssistantRAR\Contracts\IToolExecutor;
use App\AssistantRAR\Contracts\IToolRegistry;

class AssistantService implements IAssistantService
{
    public function __construct(
        private readonly IConversationService $conversation,
        private readonly IContextService $context,
        private readonly IPromptBuilder $prompt,
        private readonly IProviderManager $provider,
        private readonly IToolRegistry $registry,
        private readonly IToolExecutor $executor,
    ) {}

    public function processMessage(int $userId, int $conversationId, string $message): array
    {
        $context = $this->context->build($userId, null, null, $conversationId);

        $this->conversation->addMessage($conversationId, 'user', $message);

        $messages = $this->buildMessages($context, $message);
        $tools = $context['available_tools'] ?? [];

        $finalContent = '';
        $maxIterations = 5;

        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            $response = $this->provider->sendMessage(
                $messages,
                $iteration === 0 ? $tools : [],
                $context,
            );

            $content = $response['content'] ?? '';
            $toolCalls = $response['tool_calls'] ?? [];

            if (!empty($content)) {
                $finalContent = $content;
            }

            if (empty($toolCalls)) {
                break;
            }

            $formattedCalls = array_map(fn ($tc) => [
                'id' => $tc['id'],
                'type' => 'function',
                'function' => [
                    'name' => $tc['name'],
                    'arguments' => json_encode($tc['arguments']),
                ],
            ], $toolCalls);

            $messages[] = ['role' => 'assistant', 'content' => null, 'tool_calls' => $formattedCalls];

            foreach ($toolCalls as $call) {
                $result = $this->executor->execute($call['name'], $call['arguments'], $context);
                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $call['id'],
                    'content' => json_encode($result),
                ];
            }
        }

        if (empty($finalContent) && !empty($messages)) {
            $last = end($messages);
            if (($last['role'] ?? '') === 'tool') {
                $finalContent = 'La operación se completó.';
            }
        }

        $this->conversation->addMessage($conversationId, 'assistant', $finalContent);

        return ['content' => $finalContent];
    }

    public function processStream(int $userId, int $conversationId, string $message, callable $onChunk): void
    {
        $context = $this->context->build($userId, null, null, $conversationId);

        $this->conversation->addMessage($conversationId, 'user', $message);

        $fullContent = '';

        $this->provider->sendMessageStream(
            $this->buildMessages($context, $message),
            $context['available_tools'] ?? [],
            $context,
            function ($chunk) use ($onChunk, &$fullContent) {
                $fullContent .= $chunk;
                $onChunk($chunk);
            },
        );

        $this->conversation->addMessage($conversationId, 'assistant', $fullContent);
    }

    private function buildMessages(array $context, string $message): array
    {
        $systemPrompt = $this->prompt->buildSystemPrompt($context, $context['available_tools'] ?? []);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        if (!empty($context['history'])) {
            foreach ($context['history'] as $msg) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content'],
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        return $messages;
    }
}
