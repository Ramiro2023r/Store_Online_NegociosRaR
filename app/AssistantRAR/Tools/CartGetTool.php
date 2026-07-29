<?php

namespace App\AssistantRAR\Tools;

use App\Services\CartService;

class CartGetTool extends BaseTool
{
    public function name(): string { return 'cart.get'; }
    public function description(): string { return 'Obtener el contenido actual del carrito del usuario.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        $cart = app(CartService::class)->get($context['user']['id']);
        if (!$cart) return $this->success('Tu carrito está vacío.', ['cart' => ['items' => [], 'total' => 0]]);
        return $this->success('Carrito obtenido.', ['cart' => $cart->toArray()]);
    }
}
