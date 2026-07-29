<?php

namespace App\AssistantRAR\Tools;

use App\Services\InventoryService;

class InventoryOutOfStockTool extends BaseTool
{
    public function name(): string
    {
        return 'inventory.out_of_stock';
    }

    public function description(): string
    {
        return 'Consultar productos actualmente agotados (stock 0 o menor).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
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
        $results = $service->outOfStock($arguments['limit'] ?? 20);

        if (empty($results)) {
            return $this->success('No hay productos agotados.', ['products' => []]);
        }

        return $this->success("Se encontraron " . count($results) . " producto(s) agotados.", [
            'products' => $results,
        ]);
    }
}
