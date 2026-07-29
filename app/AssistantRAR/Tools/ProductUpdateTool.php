<?php

namespace App\AssistantRAR\Tools;

use App\Services\ProductService;

class ProductUpdateTool extends BaseTool
{
    public function name(): string
    {
        return 'product.update';
    }

    public function description(): string
    {
        return 'Actualizar los datos de un producto existente por su ID.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del producto a actualizar'],
                'name' => ['type' => 'string', 'description' => 'Nuevo nombre'],
                'description' => ['type' => 'string', 'description' => 'Nueva descripción'],
                'category_id' => ['type' => 'integer', 'description' => 'Nueva categoría'],
                'brand' => ['type' => 'string', 'description' => 'Nueva marca'],
                'sku' => ['type' => 'string', 'description' => 'Nuevo SKU'],
            ],
            'required' => ['id'],
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
        $id = $arguments['id'];
        unset($arguments['id']);

        $service = app(ProductService::class);
        $product = $service->update($id, $arguments);

        return $this->success('Producto actualizado correctamente.', [
            'product' => ['id' => $product->id, 'name' => $product->name],
        ], ['resource_type' => 'product', 'resource_id' => $id]);
    }
}
