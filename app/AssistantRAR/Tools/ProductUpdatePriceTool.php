<?php

namespace App\AssistantRAR\Tools;

use App\Services\ProductService;

class ProductUpdatePriceTool extends BaseTool
{
    public function name(): string
    {
        return 'product.update_price';
    }

    public function description(): string
    {
        return 'Actualizar el precio y opcionalmente el precio de comparación de un producto.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del producto'],
                'price' => ['type' => 'number', 'description' => 'Nuevo precio'],
                'compare_price' => ['type' => 'number', 'description' => 'Nuevo precio de comparación (opcional)'],
            ],
            'required' => ['id', 'price'],
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
        $product = $service->updatePrice(
            $arguments['id'],
            $arguments['price'],
            $arguments['compare_price'] ?? null,
        );

        return $this->success('Precio actualizado correctamente.', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'compare_price' => $product->compare_price,
            ],
        ], ['resource_type' => 'product', 'resource_id' => $arguments['id']]);
    }
}
