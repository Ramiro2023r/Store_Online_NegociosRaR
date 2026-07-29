<?php

namespace App\AssistantRAR\Tools;

use App\Services\CategoryService;

class CategoryUpdateTool extends BaseTool
{
    public function name(): string
    {
        return 'category.update';
    }

    public function description(): string
    {
        return 'Actualizar el nombre, ícono o descripción de una categoría.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID de la categoría'],
                'name' => ['type' => 'string', 'description' => 'Nuevo nombre'],
                'icon' => ['type' => 'string', 'description' => 'Nuevo ícono'],
                'description' => ['type' => 'string', 'description' => 'Nueva descripción'],
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

        $service = app(CategoryService::class);
        $category = $service->update($id, $arguments);

        return $this->success('Categoría actualizada correctamente.', [
            'category' => ['id' => $category->id, 'name' => $category->name],
        ], ['resource_type' => 'category', 'resource_id' => $id]);
    }
}
