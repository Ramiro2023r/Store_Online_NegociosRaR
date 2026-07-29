<?php

namespace App\AssistantRAR\Tools;

use App\Services\InventoryService;

class InventoryLowStockTool extends BaseTool
{
    public function name(): string
    {
        return 'inventory.low_stock';
    }

    public function description(): string
    {
        return 'Consultar productos con stock bajo (por debajo del mínimo configurado o umbral personalizado).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'threshold' => ['type' => 'integer', 'description' => 'Umbral personalizado'],
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
        $service = app(InventoryService::class);
        $results = $service->lowStock($arguments['threshold'] ?? null, $arguments['limit'] ?? 20);

        if (empty($results)) {
            return $this->success('No hay productos con stock bajo.', ['products' => []]);
        }

        return $this->success("Se encontraron " . count($results) . " producto(s) con stock bajo.", [
            'products' => $results,
        ]);
    }
}
