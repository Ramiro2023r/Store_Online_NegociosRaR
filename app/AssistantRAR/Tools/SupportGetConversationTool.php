<?php

namespace App\AssistantRAR\Tools;

use App\Services\SupportService;

class SupportGetConversationTool extends BaseTool
{
    public function name(): string { return 'support.get_conversation'; }
    public function description(): string { return 'Obtener detalle de una conversación de soporte con sus mensajes.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID de la conversación'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        $conv = app(SupportService::class)->getConversation($arguments['id']);
        if (!$conv) return $this->error('Conversación no encontrada.', 'NOT_FOUND');
        return $this->success('Conversación obtenida.', ['conversation' => $conv->toArray()]);
    }
}
