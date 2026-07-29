<?php

namespace App\AssistantRAR\Tools;

use App\Services\ProductService;

class ProductChangeStatusTool extends BaseTool
{
    public function name(): string
    {
        return 'product.change_status';
    }

    public function description(): string
    {
        return 'Activar o desactivar un producto.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del producto'],
                'active' => ['type' => 'boolean', 'description' => 'true para activar, false para desactivar'],
            ],
            'required' => ['id', 'active'],
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
        $product = $service->changeStatus($arguments['id'], $arguments['active']);

        $state = $product->active ? 'activado' : 'desactivado';

        return $this->success("Producto {$state} correctamente.", [
            'product' => ['id' => $product->id, 'name' => $product->name, 'active' => $product->active],
        ], ['resource_type' => 'product', 'resource_id' => $arguments['id']]);
    }
}
