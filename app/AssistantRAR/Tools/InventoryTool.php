<?php

namespace App\AssistantRAR\Tools;

class InventoryTool extends BaseTool
{
    public function name(): string
    {
        return 'inventory.low_stock';
    }

    public function description(): string
    {
        return 'Consultar productos con stock bajo (por debajo del mínimo configurado).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'threshold' => ['type' => 'integer', 'description' => 'Umbral personalizado de stock mínimo'],
                'limit' => ['type' => 'integer', 'description' => 'Máximo de resultados'],
            ],
        ];
    }

    public function roles(): array
    {
        return ['admin', 'trabajador'];
    }

    public function confirmationLevel(): int
    {
        return 0;
    }

    public function execute(array $context, array $arguments): array
    {
        return $this->success('Herramienta no implementada. Usa el módulo de inventario para esta acción.');
    }
}
