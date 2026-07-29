<?php

namespace App\AssistantRAR\Tools;

use App\Services\LoyaltyService;

class LoyaltyGetBalanceTool extends BaseTool
{
    public function name(): string { return 'loyalty.get_balance'; }
    public function description(): string { return 'Consultar el saldo de puntos de fidelización del usuario.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        $balance = app(LoyaltyService::class)->getBalance($context['user']['id']);
        return $this->success("Tienes {$balance} puntos de fidelización.", ['balance' => $balance]);
    }
}
