<?php

namespace App\AssistantRAR\Tools;

use App\Services\UserService;

class UserUpdateTool extends BaseTool
{
    public function name(): string
    {
        return 'user.update';
    }

    public function description(): string
    {
        return 'Actualizar datos de un usuario: nombre, email, teléfono, contraseña. Solo administradores.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del usuario'],
                'name' => ['type' => 'string', 'description' => 'Nuevo nombre'],
                'email' => ['type' => 'string', 'description' => 'Nuevo email'],
                'phone' => ['type' => 'string', 'description' => 'Nuevo teléfono'],
                'password' => ['type' => 'string', 'description' => 'Nueva contraseña'],
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
        return 1;
    }

    public function execute(array $context, array $arguments): array
    {
        $id = $arguments['id'];
        unset($arguments['id']);

        $service = app(UserService::class);
        $user = $service->update($id, $arguments);

        return $this->success("Usuario '{$user->name}' actualizado correctamente.", [
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ], ['resource_type' => 'user', 'resource_id' => $id]);
    }
}
