<?php

namespace App\AssistantRAR\Tools;

use App\Services\UserService;

class UserSearchTool extends BaseTool
{
    public function name(): string
    {
        return 'user.search';
    }

    public function description(): string
    {
        return 'Buscar usuarios por nombre, email o rol. Solo administradores.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Término de búsqueda'],
                'role' => ['type' => 'string', 'enum' => ['admin', 'trabajador', 'cliente'], 'description' => 'Filtrar por rol'],
                'active' => ['type' => 'boolean', 'description' => 'Filtrar por estado'],
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
        $service = app(UserService::class);
        $results = $service->search($arguments, $arguments['limit'] ?? 20);

        $users = collect($results['data'] ?? [])->map(fn ($u) => [
            'id' => $u['id'],
            'name' => $u['name'],
            'email' => $u['email'],
            'role' => $u['role'],
            'active' => $u['active'],
            'loyalty_points' => $u['loyalty_points'],
        ]);

        if ($users->isEmpty()) {
            return $this->success('No se encontraron usuarios.', ['users' => []]);
        }

        return $this->success("Se encontraron {$users->count()} usuario(s).", [
            'users' => $users->values()->toArray(),
        ]);
    }
}
