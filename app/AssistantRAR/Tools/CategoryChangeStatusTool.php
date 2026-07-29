<?php

namespace App\AssistantRAR\Tools;

use App\Services\CategoryService;

class CategoryChangeStatusTool extends BaseTool
{
    public function name(): string
    {
        return 'category.change_status';
    }

    public function description(): string
    {
        return 'Activar o desactivar una categoría.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID de la categoría'],
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
        $service = app(CategoryService::class);
        $category = $service->changeStatus($arguments['id'], $arguments['active']);

        $state = $category->active ? 'activada' : 'desactivada';

        return $this->success("Categoría {$state} correctamente.", [
            'category' => ['id' => $category->id, 'name' => $category->name, 'active' => $category->active],
        ], ['resource_type' => 'category', 'resource_id' => $arguments['id']]);
    }
}
