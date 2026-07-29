<?php

namespace App\AssistantRAR\Tools;

use App\Services\SupportService;

class SupportConversationsTool extends BaseTool
{
    public function name(): string { return 'support.conversations'; }
    public function description(): string { return 'Listar conversaciones de soporte, opcionalmente filtrar por estado.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'status' => ['type' => 'string', 'enum' => ['open', 'closed'], 'description' => 'Filtrar por estado'],
        ]];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Conversaciones obtenidas.', ['conversations' => app(SupportService::class)->conversations($arguments['status'] ?? null)]);
    }
}
