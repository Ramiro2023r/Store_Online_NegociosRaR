<?php

namespace App\AssistantRAR\Tools;

use App\Services\CartService;

class CartRemoveItemTool extends BaseTool
{
    public function name(): string { return 'cart.remove_item'; }
    public function description(): string { return 'Eliminar un producto del carrito.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'item_id' => ['type' => 'integer', 'description' => 'ID del item en el carrito'],
        ], 'required' => ['item_id']];
    }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(CartService::class)->removeItem($context['user']['id'], $arguments['item_id']);
        return $this->success('Producto eliminado del carrito.');
    }
}
