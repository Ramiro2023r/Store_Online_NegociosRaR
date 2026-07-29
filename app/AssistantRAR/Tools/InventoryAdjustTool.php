<?php

namespace App\AssistantRAR\Tools;

use App\Services\InventoryService;

class InventoryAdjustTool extends BaseTool
{
    public function name(): string
    {
        return 'inventory.adjust';
    }

    public function description(): string
    {
        return 'Ajustar el stock de un producto o variante a un valor específico.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'product_id' => ['type' => 'integer', 'description' => 'ID del producto'],
                'new_stock' => ['type' => 'integer', 'description' => 'Nuevo valor de stock'],
                'variant_id' => ['type' => 'integer', 'description' => 'ID de la variante (opcional)'],
                'notes' => ['type' => 'string', 'description' => 'Motivo del ajuste'],
            ],
            'required' => ['product_id', 'new_stock'],
        ];
    }

    public function roles(): array
    {
        return ['admin', 'trabajador'];
    }

    public function confirmationLevel(): int
    {
        return 1;
    }

    public function execute(array $context, array $arguments): array
    {
        $service = app(InventoryService::class);
        $service->adjustStock(
            $arguments['product_id'],
            $arguments['new_stock'],
            $arguments['variant_id'] ?? null,
            $arguments['notes'] ?? null,
        );

        return $this->success('Stock ajustado correctamente.', [], [
            'resource_type' => 'product', 'resource_id' => $arguments['product_id'],
        ]);
    }
}
