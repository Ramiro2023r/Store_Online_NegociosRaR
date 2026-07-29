<?php

namespace App\AssistantRAR\Tools;

use App\Services\ProductService;

class ProductDeleteTool extends BaseTool
{
    public function name(): string
    {
        return 'product.delete';
    }

    public function description(): string
    {
        return 'Eliminar un producto permanentemente por su ID.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del producto a eliminar'],
            ],
            'required' => ['id'],
        ];
    }

    public function roles(): array
    {
        return ['admin'];
    }

    public function confirmationLevel(): int
    {
        return 2;
    }

    public function execute(array $context, array $arguments): array
    {
        $service = app(ProductService::class);
        $service->delete($arguments['id']);

        return $this->success('Producto eliminado correctamente.', [], [
            'resource_type' => 'product', 'resource_id' => $arguments['id'],
        ]);
    }
}
