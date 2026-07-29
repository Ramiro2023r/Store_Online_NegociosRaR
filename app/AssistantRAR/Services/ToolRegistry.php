<?php

namespace App\AssistantRAR\Services;

use App\AssistantRAR\Contracts\IToolRegistry;

class ToolRegistry implements IToolRegistry
{
    private array $tools = [];

    public function register(string $toolClass): void
    {
        $instance = app($toolClass);
        $this->tools[$instance->name()] = [
            'name' => $instance->name(),
            'description' => $instance->description(),
            'input_schema' => $instance->inputSchema(),
            'handler' => $toolClass,
            'roles' => $instance->roles(),
            'confirmation_level' => $instance->confirmationLevel(),
            'enabled' => true,
        ];
    }

    public function get(string $name): ?array
    {
        return $this->tools[$name] ?? null;
    }

    public function getAll(): array
    {
        return array_values($this->tools);
    }

    public function getForRole(string $role): array
    {
        return array_values(array_filter($this->tools, fn ($t) => in_array($role, $t['roles'])));
    }

    public function getEnabled(): array
    {
        return array_values(array_filter($this->tools, fn ($t) => $t['enabled']));
    }
}
