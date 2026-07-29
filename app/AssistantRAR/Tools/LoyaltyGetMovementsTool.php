<?php

namespace App\AssistantRAR\Tools;

use App\Services\LoyaltyService;

class LoyaltyGetMovementsTool extends BaseTool
{
    public function name(): string { return 'loyalty.get_movements'; }
    public function description(): string { return 'Consultar movimientos recientes de puntos de fidelización.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => ['limit' => ['type' => 'integer']]]; }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Movimientos obtenidos.', ['movements' => app(LoyaltyService::class)->getMovements($context['user']['id'], $arguments['limit'] ?? 20)]);
    }
}
