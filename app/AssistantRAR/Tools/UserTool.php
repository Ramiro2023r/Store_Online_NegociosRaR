<?php

namespace App\AssistantRAR\Tools;

class UserTool extends BaseTool
{
    public function name(): string
    {
        return 'user.search';
    }

    public function description(): string
    {
        return 'Buscar usuarios por nombre, email o rol.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Término de búsqueda'],
                'role' => ['type' => 'string', 'enum' => ['admin', 'trabajador', 'cliente'], 'description' => 'Filtrar por rol'],
                'active' => ['type' => 'boolean', 'description' => 'Filtrar por estado activo/inactivo'],
                'limit' => ['type' => 'integer', 'description' => 'Máximo de resultados'],
            ],
        ];
    }

    public function roles(): array
    {
        return ['admin'];
    }

    public function confirmationLevel(): int
    {
        return 0;
    }

    public function execute(array $context, array $arguments): array
    {
        return $this->success('Herramienta no implementada. Usa el panel de usuarios para esta acción.');
    }
}
