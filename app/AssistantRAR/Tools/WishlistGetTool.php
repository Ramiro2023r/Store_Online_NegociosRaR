<?php

namespace App\AssistantRAR\Tools;

use App\Services\WishlistService;

class WishlistGetTool extends BaseTool
{
    public function name(): string { return 'wishlist.get'; }
    public function description(): string { return 'Obtener la lista de deseos del usuario.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        $items = app(WishlistService::class)->get($context['user']['id']);
        return $this->success('Lista de deseos obtenida.', ['items' => $items]);
    }
}
