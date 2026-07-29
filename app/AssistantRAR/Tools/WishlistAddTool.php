<?php

namespace App\AssistantRAR\Tools;

use App\Services\WishlistService;

class WishlistAddTool extends BaseTool
{
    public function name(): string { return 'wishlist.add'; }
    public function description(): string { return 'Agregar un producto a la lista de deseos.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'product_id' => ['type' => 'integer', 'description' => 'ID del producto'],
        ], 'required' => ['product_id']];
    }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(WishlistService::class)->add($context['user']['id'], $arguments['product_id']);
        return $this->success('Producto agregado a la lista de deseos.');
    }
}
