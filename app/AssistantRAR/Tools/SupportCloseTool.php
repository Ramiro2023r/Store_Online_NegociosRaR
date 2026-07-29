<?php

namespace App\AssistantRAR\Tools;

use App\Services\SupportService;

class SupportCloseTool extends BaseTool
{
    public function name(): string { return 'support.close_conversation'; }
    public function description(): string { return 'Cerrar una conversación de soporte.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(SupportService::class)->close($arguments['id']);
        return $this->success('Conversación cerrada.', [], ['resource_type' => 'support_conversation', 'resource_id' => $arguments['id']]);
    }
}
