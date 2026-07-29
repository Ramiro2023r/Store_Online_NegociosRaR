<?php

namespace App\AssistantRAR\Tools;

use App\Services\UserService;

class UserChangeRoleTool extends BaseTool
{
    public function name(): string
    {
        return 'user.change_role';
    }

    public function description(): string
    {
        return 'Cambiar el rol de un usuario. Solo administradores.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del usuario'],
                'role' => ['type' => 'string', 'enum' => ['admin', 'trabajador', 'cliente'], 'description' => 'Nuevo rol'],
            ],
            'required' => ['id', 'role'],
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
        $service = app(UserService::class);
        $user = $service->changeRole($arguments['id'], $arguments['role']);

        return $this->success("Rol de '{$user->name}' cambiado a '{$user->role}'.", [
            'user' => ['id' => $user->id, 'name' => $user->name, 'role' => $user->role],
        ], ['resource_type' => 'user', 'resource_id' => $arguments['id']]);
    }
}
