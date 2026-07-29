<?php

namespace App\AssistantRAR\Tools;

use App\Services\UserService;

class UserCreateWorkerTool extends BaseTool
{
    public function name(): string
    {
        return 'user.create_worker';
    }

    public function description(): string
    {
        return 'Crear un nuevo usuario con rol de trabajador. Solo administradores.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nombre completo'],
                'email' => ['type' => 'string', 'format' => 'email', 'description' => 'Correo electrónico'],
                'password' => ['type' => 'string', 'description' => 'Contraseña (opcional, se genera automáticamente)'],
                'phone' => ['type' => 'string', 'description' => 'Teléfono'],
            ],
            'required' => ['name', 'email'],
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
        $service = app(UserService::class);
        $user = $service->createWorker($arguments);

        return $this->success("Trabajador '{$user->name}' creado correctamente.", [
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role],
        ], ['resource_type' => 'user', 'resource_id' => $user->id]);
    }
}
