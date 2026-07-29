<?php

namespace App\AssistantRAR\Contracts;

interface IToolRegistry
{
    public function register(string $toolClass): void;
    public function get(string $name): ?array;
    public function getAll(): array;
    public function getForRole(string $role): array;
    public function getEnabled(): array;
}
