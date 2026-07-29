<?php

namespace App\AssistantRAR\Tools;

use App\Services\ProductService;

class ProductCreateTool extends BaseTool
{
    public function name(): string
    {
        return 'product.create';
    }

    public function description(): string
    {
        return 'Crear un nuevo producto con nombre, precio, stock, categoría y datos opcionales.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nombre del producto'],
                'price' => ['type' => 'number', 'description' => 'Precio del producto'],
                'stock' => ['type' => 'integer', 'description' => 'Stock inicial'],
                'category_id' => ['type' => 'integer', 'description' => 'ID de la categoría'],
                'description' => ['type' => 'string', 'description' => 'Descripción del producto'],
                'sku' => ['type' => 'string', 'description' => 'Código SKU único'],
                'brand' => ['type' => 'string', 'description' => 'Marca del producto'],
                'compare_price' => ['type' => 'number', 'description' => 'Precio de comparación (tachado)'],
                'active' => ['type' => 'boolean', 'description' => 'Producto activo'],
            ],
            'required' => ['name', 'price', 'stock', 'category_id'],
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
        $product = $service->create($arguments);

        return $this->success('Producto creado correctamente.', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
            ],
        ], ['resource_type' => 'product', 'resource_id' => $product->id]);
    }
}
