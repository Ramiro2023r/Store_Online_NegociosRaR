<?php

namespace App\AssistantRAR\Tools;

use App\Services\AddressService;

class AddressUpdateTool extends BaseTool
{
    public function name(): string { return 'address.update'; }
    public function description(): string { return 'Actualizar una dirección existente.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
            'label' => ['type' => 'string'],
            'address' => ['type' => 'string'],
            'city' => ['type' => 'string'],
            'phone' => ['type' => 'string'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $id = $arguments['id']; unset($arguments['id']);
        $addr = app(AddressService::class)->update($context['user']['id'], $id, $arguments);
        return $this->success('Dirección actualizada.', ['address' => $addr->toArray()], ['resource_type' => 'address', 'resource_id' => $id]);
    }
}
