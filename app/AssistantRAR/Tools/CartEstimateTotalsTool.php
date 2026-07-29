<?php

namespace App\AssistantRAR\Tools;

use App\Services\CartService;

class CartEstimateTotalsTool extends BaseTool
{
    public function name(): string { return 'cart.estimate_totals'; }
    public function description(): string { return 'Estimar el subtotal y cantidad de items del carrito.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        $totals = app(CartService::class)->estimateTotals($context['user']['id']);
        return $this->success('Totales estimados.', $totals);
    }
}
