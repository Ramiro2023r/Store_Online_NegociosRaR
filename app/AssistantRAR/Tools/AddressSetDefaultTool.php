<?php

namespace App\AssistantRAR\Tools;

use App\Services\AddressService;

class AddressSetDefaultTool extends BaseTool
{
    public function name(): string { return 'address.set_default'; }
    public function description(): string { return 'Marcar una dirección como predeterminada.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(AddressService::class)->setDefault($context['user']['id'], $arguments['id']);
        return $this->success('Dirección predeterminada actualizada.', [], ['resource_type' => 'address', 'resource_id' => $arguments['id']]);
    }
}
