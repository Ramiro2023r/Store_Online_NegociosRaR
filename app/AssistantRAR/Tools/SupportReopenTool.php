<?php

namespace App\AssistantRAR\Tools;

use App\Services\SupportService;

class SupportReopenTool extends BaseTool
{
    public function name(): string { return 'support.reopen_conversation'; }
    public function description(): string { return 'Reabrir una conversación de soporte cerrada.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(SupportService::class)->reopen($arguments['id']);
        return $this->success('Conversación reabierta.', [], ['resource_type' => 'support_conversation', 'resource_id' => $arguments['id']]);
    }
}
