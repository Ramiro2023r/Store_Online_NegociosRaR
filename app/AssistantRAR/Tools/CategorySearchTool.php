<?php

namespace App\AssistantRAR\Tools;

use App\Services\CategoryService;

class CategorySearchTool extends BaseTool
{
    public function name(): string
    {
        return 'category.search';
    }

    public function description(): string
    {
        return 'Buscar categorías por nombre o estado.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Término de búsqueda'],
                'active' => ['type' => 'boolean', 'description' => 'Filtrar por activo/inactivo'],
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
        $service = app(CategoryService::class);
        $results = $service->search($arguments);

        $categories = array_map(fn ($c) => [
            'id' => $c['id'],
            'name' => $c['name'],
            'icon' => $c['icon'],
            'active' => $c['active'],
            'products_count' => $c['products_count'] ?? 0,
        ], $results);

        if (empty($categories)) {
            return $this->success('No se encontraron categorías.', ['categories' => []]);
        }

        return $this->success("Se encontraron " . count($categories) . " categoría(s).", [
            'categories' => $categories,
        ]);
    }
}
