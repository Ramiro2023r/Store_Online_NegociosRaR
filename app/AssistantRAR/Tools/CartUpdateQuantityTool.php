<?php

namespace App\AssistantRAR\Tools;

use App\Services\CartService;

class CartUpdateQuantityTool extends BaseTool
{
    public function name(): string { return 'cart.update_quantity'; }
    public function description(): string { return 'Actualizar la cantidad de un producto en el carrito.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'item_id' => ['type' => 'integer', 'description' => 'ID del item en el carrito'],
            'quantity' => ['type' => 'integer', 'description' => 'Nueva cantidad (mínimo 1)'],
        ], 'required' => ['item_id', 'quantity']];
    }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $item = app(CartService::class)->updateQuantity($context['user']['id'], $arguments['item_id'], $arguments['quantity']);
        return $this->success('Cantidad actualizada.', ['item' => $item->toArray()]);
    }
}
