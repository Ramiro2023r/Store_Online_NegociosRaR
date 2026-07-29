<?php

namespace App\AssistantRAR\Tools;

use App\Services\ProductService;

class ProductTool extends BaseTool
{
    public function name(): string
    {
        return 'product.search';
    }

    public function description(): string
    {
        return 'Buscar productos por nombre, categoría, marca, rango de precio o estado.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Término de búsqueda'],
                'category_id' => ['type' => 'integer', 'description' => 'Filtrar por categoría'],
                'brand' => ['type' => 'string', 'description' => 'Filtrar por marca'],
                'min_price' => ['type' => 'number', 'description' => 'Precio mínimo'],
                'max_price' => ['type' => 'number', 'description' => 'Precio máximo'],
                'status' => ['type' => 'string', 'enum' => ['active', 'inactive'], 'description' => 'Estado del producto'],
                'limit' => ['type' => 'integer', 'description' => 'Máximo de resultados'],
            ],
        ];
    }

    public function roles(): array
    {
        return ['admin', 'trabajador', 'cliente'];
    }

    public function confirmationLevel(): int
    {
        return 0;
    }

    public function execute(array $context, array $arguments): array
    {
        $service = app(ProductService::class);
        $results = $service->search($arguments, $arguments['limit'] ?? 10);

        $products = collect($results['data'] ?? [])->map(fn ($p) => [
            'id' => $p['id'],
            'name' => $p['name'],
            'price' => $p['price'],
            'stock' => $p['stock'],
            'brand' => $p['brand'],
            'active' => $p['active'],
        ]);

        if ($products->isEmpty()) {
            return $this->success('No se encontraron productos con los criterios indicados.', ['products' => []]);
        }

        return $this->success("Se encontraron {$products->count()} producto(s).", [
            'products' => $products->values()->toArray(),
            'total' => $results['total'] ?? count($products),
        ]);
    }
}

