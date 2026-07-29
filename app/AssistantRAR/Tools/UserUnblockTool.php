<?php

namespace App\AssistantRAR\Tools;

use App\Services\UserService;

class UserUnblockTool extends BaseTool
{
    public function name(): string
    {
        return 'user.unblock';
    }

    public function description(): string
    {
        return 'Desbloquear (reactivar) un usuario. Solo administradores.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del usuario a desbloquear'],
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
        $service = app(UserService::class);
        $user = $service->unblock($arguments['id']);

        return $this->success("Usuario '{$user->name}' desbloqueado.", [
            'user' => ['id' => $user->id, 'name' => $user->name, 'active' => $user->active],
        ], ['resource_type' => 'user', 'resource_id' => $arguments['id']]);
    }
}
