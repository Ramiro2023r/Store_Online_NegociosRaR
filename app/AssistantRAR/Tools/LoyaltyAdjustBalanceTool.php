<?php

namespace App\AssistantRAR\Tools;

use App\Services\LoyaltyService;

class LoyaltyAdjustBalanceTool extends BaseTool
{
    public function name(): string { return 'loyalty.adjust_balance'; }
    public function description(): string { return 'Ajustar (sumar/restar) puntos de fidelización a un usuario.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'user_id' => ['type' => 'integer'],
            'points' => ['type' => 'integer', 'description' => 'Puntos a ajustar (positivo suma, negativo resta)'],
            'reason' => ['type' => 'string', 'description' => 'Motivo del ajuste'],
        ], 'required' => ['user_id', 'points', 'reason']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 2; }
    public function execute(array $context, array $arguments): array
    {
        $user = app(LoyaltyService::class)->adjustBalance($arguments['user_id'], $arguments['points'], $arguments['reason']);
        return $this->success("Puntos ajustados. Nuevo saldo: {$user->loyalty_points}.", ['user' => ['id' => $user->id, 'loyalty_points' => $user->loyalty_points]], ['resource_type' => 'user', 'resource_id' => $arguments['user_id']]);
    }
}
