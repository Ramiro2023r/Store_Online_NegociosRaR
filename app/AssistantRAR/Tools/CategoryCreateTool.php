<?php

namespace App\AssistantRAR\Tools;

use App\Services\CategoryService;

class CategoryCreateTool extends BaseTool
{
    public function name(): string
    {
        return 'category.create';
    }

    public function description(): string
    {
        return 'Crear una nueva categoría con nombre, ícono y descripción opcional.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nombre de la categoría'],
                'icon' => ['type' => 'string', 'description' => 'Ícono emoji (ej: 📦)'],
                'description' => ['type' => 'string', 'description' => 'Descripción'],
            ],
            'required' => ['name'],
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
        $service = app(CategoryService::class);
        $category = $service->create($arguments);

        return $this->success('Categoría creada correctamente.', [
            'category' => ['id' => $category->id, 'name' => $category->name],
        ], ['resource_type' => 'category', 'resource_id' => $category->id]);
    }
}
