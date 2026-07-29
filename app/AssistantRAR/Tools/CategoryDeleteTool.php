<?php

namespace App\AssistantRAR\Tools;

use App\Services\CategoryService;

class CategoryDeleteTool extends BaseTool
{
    public function name(): string
    {
        return 'category.delete';
    }

    public function description(): string
    {
        return 'Eliminar una categoría permanentemente por su ID.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID de la categoría a eliminar'],
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
        $service = app(CategoryService::class);
        $service->delete($arguments['id']);

        return $this->success('Categoría eliminada correctamente.', [], [
            'resource_type' => 'category', 'resource_id' => $arguments['id'],
        ]);
    }
}
