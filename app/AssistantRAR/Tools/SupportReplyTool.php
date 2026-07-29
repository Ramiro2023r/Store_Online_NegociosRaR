<?php

namespace App\AssistantRAR\Tools;

use App\Services\SupportService;

class SupportReplyTool extends BaseTool
{
    public function name(): string { return 'support.reply'; }
    public function description(): string { return 'Responder a una conversación de soporte como staff.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'conversation_id' => ['type' => 'integer'],
            'body' => ['type' => 'string', 'description' => 'Texto de la respuesta'],
        ], 'required' => ['conversation_id', 'body']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $msg = app(SupportService::class)->reply($arguments['conversation_id'], $arguments['body'], $context['user']['id']);
        return $this->success('Respuesta enviada.', ['message' => $msg->toArray()], ['resource_type' => 'support_conversation', 'resource_id' => $arguments['conversation_id']]);
    }
}
