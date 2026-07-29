<?php

namespace App\AssistantRAR\Tools;

use App\Services\AddressService;

class AddressCreateTool extends BaseTool
{
    public function name(): string { return 'address.create'; }
    public function description(): string { return 'Crear una nueva dirección para el usuario.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'label' => ['type' => 'string', 'description' => 'Etiqueta (ej: Casa, Trabajo)'],
            'address' => ['type' => 'string', 'description' => 'Dirección completa'],
            'city' => ['type' => 'string', 'description' => 'Ciudad'],
            'phone' => ['type' => 'string', 'description' => 'Teléfono'],
            'is_default' => ['type' => 'boolean', 'description' => 'Marcar como predeterminada'],
        ], 'required' => ['label', 'address', 'city']];
    }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $addr = app(AddressService::class)->create($context['user']['id'], $arguments);
        return $this->success('Dirección creada.', ['address' => $addr->toArray()], ['resource_type' => 'address', 'resource_id' => $addr->id]);
    }
}
