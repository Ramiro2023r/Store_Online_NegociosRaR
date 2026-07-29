<?php

namespace App\AssistantRAR\Tools;

use App\AssistantRAR\Contracts\IAssistantTool;
use App\AssistantRAR\DTO\ToolResult;

abstract class BaseTool implements IAssistantTool
{
    abstract public function name(): string;
    abstract public function description(): string;
    abstract public function inputSchema(): array;
    abstract public function roles(): array;
    abstract public function confirmationLevel(): int;

    abstract public function execute(array $context, array $arguments): array;

    protected function success(string $message, array $data = [], array $metadata = []): array
    {
        return ToolResult::success($message, $data, $metadata)->toArray();
    }

    protected function error(string $message, string $code = 'INTERNAL_ERROR'): array
    {
        return ToolResult::error($message, $code)->toArray();
    }

    protected function requireRole(array $context, string $role): bool
    {
        return ($context['user']['role'] ?? '') === $role;
    }

    protected function isAdmin(array $context): bool
    {
        return ($context['user']['role'] ?? '') === 'admin';
    }

    protected function isStaff(array $context): bool
    {
        return in_array($context['user']['role'] ?? '', ['admin', 'trabajador']);
    }
}
