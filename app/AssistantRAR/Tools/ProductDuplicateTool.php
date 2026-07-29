<?php

namespace App\AssistantRAR\Tools;

use App\Services\ProductService;

class ProductDuplicateTool extends BaseTool
{
    public function name(): string
    {
        return 'product.duplicate';
    }

    public function description(): string
    {
        return 'Duplicar un producto existente con todos sus datos.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del producto a duplicar'],
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
        $service = app(ProductService::class);
        $product = $service->duplicate($arguments['id']);

        return $this->success('Producto duplicado correctamente.', [
            'product' => ['id' => $product->id, 'name' => $product->name],
        ], ['resource_type' => 'product', 'resource_id' => $product->id]);
    }
}
