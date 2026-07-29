<?php

namespace App\AssistantRAR\Tools;

use App\Services\InventoryService;

class InventorySetMinimumStockTool extends BaseTool
{
    public function name(): string
    {
        return 'inventory.set_minimum_stock';
    }

    public function description(): string
    {
        return 'Establecer el stock mínimo de alerta para un producto.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'product_id' => ['type' => 'integer', 'description' => 'ID del producto'],
                'min_stock' => ['type' => 'integer', 'description' => 'Stock mínimo para alerta'],
            ],
            'required' => ['product_id', 'min_stock'],
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
        $product = $service->setMinimumStock($arguments['product_id'], $arguments['min_stock']);

        return $this->success("Stock mínimo actualizado a {$product->min_stock} para '{$product->name}'.", [
            'product' => ['id' => $product->id, 'name' => $product->name, 'min_stock' => $product->min_stock],
        ], ['resource_type' => 'product', 'resource_id' => $arguments['product_id']]);
    }
}
