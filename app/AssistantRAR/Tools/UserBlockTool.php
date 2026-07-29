<?php

namespace App\AssistantRAR\Tools;

use App\Services\UserService;

class UserBlockTool extends BaseTool
{
    public function name(): string
    {
        return 'user.block';
    }

    public function description(): string
    {
        return 'Bloquear (desactivar) un usuario. Solo administradores.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del usuario a bloquear'],
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
        $service = app(UserService::class);
        $user = $service->block($arguments['id']);

        return $this->success("Usuario '{$user->name}' bloqueado.", [
            'user' => ['id' => $user->id, 'name' => $user->name, 'active' => $user->active],
        ], ['resource_type' => 'user', 'resource_id' => $arguments['id']]);
    }
}
