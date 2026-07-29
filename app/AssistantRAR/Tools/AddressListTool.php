<?php

namespace App\AssistantRAR\Tools;

use App\Services\AddressService;

class AddressListTool extends BaseTool
{
    public function name(): string { return 'address.list'; }
    public function description(): string { return 'Listar direcciones guardadas del usuario.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Direcciones obtenidas.', ['addresses' => app(AddressService::class)->list($context['user']['id'])]);
    }
}
