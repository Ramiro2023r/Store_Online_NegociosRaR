<?php

namespace App\AssistantRAR\Tools;

use App\Services\ProductService;

class ProductUpdateStockTool extends BaseTool
{
    public function name(): string
    {
        return 'product.update_stock';
    }

    public function description(): string
    {
        return 'Actualizar el stock disponible de un producto.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del producto'],
                'stock' => ['type' => 'integer', 'description' => 'Nuevo stock (mínimo 0)'],
            ],
            'required' => ['id', 'stock'],
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
        $service = app(ProductService::class);
        $product = $service->updateStock($arguments['id'], $arguments['stock']);

        return $this->success('Stock actualizado correctamente.', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'stock' => $product->stock,
            ],
        ], ['resource_type' => 'product', 'resource_id' => $arguments['id']]);
    }
}
