<?php

namespace App\AssistantRAR\Tools;

use App\Services\CartService;

class CartAddItemTool extends BaseTool
{
    public function name(): string { return 'cart.add_item'; }
    public function description(): string { return 'Agregar un producto al carrito del usuario.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'product_id' => ['type' => 'integer', 'description' => 'ID del producto'],
            'quantity' => ['type' => 'integer', 'description' => 'Cantidad (default 1)'],
            'variant_id' => ['type' => 'integer', 'description' => 'ID de variante (opcional)'],
        ], 'required' => ['product_id']];
    }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $item = app(CartService::class)->addItem($context['user']['id'], $arguments['product_id'], $arguments['quantity'] ?? 1, $arguments['variant_id'] ?? null);
        return $this->success('Producto agregado al carrito.', ['item' => $item->toArray()]);
    }
}
